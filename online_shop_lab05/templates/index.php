/**
 * This script fetches the latest products from the database and their associated images.
 * 
 * - Connects to the database using the `Database` class.
 * - Retrieves the two most recently created products from the `products` table.
 * - For each product, fetches its associated image paths from the `product_images` table.
 * - Adds the image paths to the respective product's data under the `images` key.
 * 
 * Variables:
 * - `$db`: Instance of the `Database` class used for database operations.
 * - `$stmt`: Prepared statement for fetching the latest products.
 * - `$latestProducts`: Array containing the latest products with their details.
 * - `$stmtImg`: Query for fetching image paths associated with a product.
 * - `$images`: Array of image paths for a specific product.
 * 
 * Output:
 * - The script uses output buffering (`ob_start()`) to capture the output for further processing.
 */
<?php
require_once __DIR__ . '/../src/db.php';


$db = new Database();

$stmt = $db->getPdo()->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT 2");
$stmt->execute();
$latestProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($latestProducts as &$product) {
    $stmtImg = $db->query("SELECT image_path FROM product_images WHERE product_id = :id", [':id' => $product['id']]);
    $images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
    $product['images'] = $images;
}
unset($product);

ob_start();
?>
<h1>Последние товары</h1>
<?php foreach ($latestProducts as $product): ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <strong><?= htmlspecialchars($product['name']) ?></strong><br>
        Цена: <?= htmlspecialchars($product['price']) ?> MDL<br>
        Регион: <?= htmlspecialchars($product['region']) ?><br>
        <small>Опубликовано: <?= $product['created_at'] ?></small><br><br>
        <?php if (!empty($product['images'])): ?>
            <div style="display: flex; gap: 10px;">
                <?php foreach ($product['images'] as $img): ?>
                    <img src="/image.php?img=<?= urlencode(basename($img)) ?>" alt="Фото товара" style="max-width:200px; max-height:150px; margin-right:10px;">
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <em>Нет изображений</em>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php
$content = ob_get_clean();
$title = "Главная";
include __DIR__ . '/layout.php';
?>
