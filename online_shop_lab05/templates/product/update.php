/**
 * This script handles the update functionality for a product in an online shop.
 * 
 * It performs the following tasks:
 * - Includes necessary dependencies for database operations and helper functions.
 * - Initializes a database connection.
 * - Checks if a product ID is provided via the `id` query parameter.
 * - Fetches the product details from the database using the provided product ID.
 * - If the product is not found, displays an error message and terminates execution.
 * - Retrieves associated images for the product from the database.
 * - Stores the product details and associated images for further processing.
 * 
 * Dependencies:
 * - `/../../src/db.php`: Contains the `Database` class for database operations.
 * - `/../../src/helpers.php`: Contains helper functions.
 * 
 * URL Parameters:
 * - `id` (required): The ID of the product to be updated.
 * 
 * Database Queries:
 * - Fetches product details from the `products` table using the product ID.
 * - Fetches associated image paths from the `product_images` table using the product ID.
 * 
 * Output:
 * - If the product is not found, an error message is displayed.
 * - Product details and associated images are prepared for further use.
 */
<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';

$db = new Database();

if (!isset($_GET['id'])) {
    header("Location: /product");
    exit;
}
$productId = $_GET['id'];
$stmt = $db->query("SELECT * FROM products WHERE id = :id", [':id' => $productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    echo "<p>Продукт не найден</p>";
    exit;
}

$stmt = $db->query("SELECT image_path FROM product_images WHERE product_id = :id", [':id' => $productId]);
$images = $stmt->fetchAll(PDO::FETCH_COLUMN);
$product['images'] = $images;

ob_start();
?>
<h1>Редактирование товара</h1>
<form action="/product/update" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
    
    <label for="name">Название:</label><br>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>
    
    <label for="description">Описание:</label><br>
    <textarea name="description" id="description" required><?= htmlspecialchars($product['description']) ?></textarea><br><br>
    
    <label for="price">Цена:</label><br>
    <input type="number" name="price" id="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required><br><br>
    
    <label for="category">Категория:</label><br>
    <input type="text" name="category" id="category" value="<?= htmlspecialchars($product['category']) ?>" required><br><br>
    
    <label for="phone">Номер телефона продавца:</label><br>
    <input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($product['phone']) ?>" placeholder="+373xxxxxxxx" required><br><br>
    
    <label for="region">Регион:</label><br>
    <select name="region" id="region" required>
        <option value="" disabled>Выберите регион</option>
        <?php foreach ($regions as $region): ?>
            <option value="<?= htmlspecialchars($region) ?>" <?= ($product['region'] === $region) ? 'selected' : '' ?>>
                <?= htmlspecialchars($region) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>
    
    <label>
        <input type="checkbox" name="is_bargain" value="1" <?= ($product['is_bargain']) ? 'checked' : '' ?>>
        Возможен торг
    </label><br><br>
    
    <label for="images">Добавить изображения (необязательно):</label><br>
    <input type="file" name="images[]" id="images" multiple><br><br>
    
    <button type="submit">Сохранить изменения</button>
</form>

<h3>Текущие изображения</h3>
<?php if (!empty($product['images'])): ?>
    <div style="display: flex; gap: 10px;">
        <?php foreach ($product['images'] as $img): ?>
            <img src="/image.php?img=<?= urlencode(basename($img)) ?>" alt="Фото товара" style="max-width:200px; max-height:150px;">
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <em>Нет изображений</em>
<?php endif; ?>

<?php
$content = ob_get_clean();
$title = "Редактирование товара";
include __DIR__ . '/../layout.php';
?>
