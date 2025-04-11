<?php
require_once __DIR__ . '/../src/db.php';


$db = new Database();

// Получаем два последних товара
$stmt = $db->getPdo()->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT 2");
$stmt->execute();
$latestProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем изображения для каждого товара
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
