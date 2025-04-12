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
