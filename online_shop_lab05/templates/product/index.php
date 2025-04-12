<?php
require_once __DIR__ . '/../../src/db.php';

$db = new Database();

$stmt = $db->query("SELECT COUNT(*) FROM products");
$totalProducts = (int)$stmt->fetchColumn();

$perPage = 5;
$totalPages = ($totalProducts > 0) ? ceil($totalProducts / $perPage) : 1;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $perPage;

$stmt = $db->getPdo()->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$paginatedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($paginatedProducts as &$product) {
    $stmtImg = $db->query("SELECT image_path FROM product_images WHERE product_id = :id", [':id' => $product['id']]);
    $images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
    $product['images'] = $images;
}
unset($product);

ob_start();
?>
<h1>Все товары — Страница <?= $page ?></h1>

<?php foreach ($paginatedProducts as $product): ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <strong><?= htmlspecialchars($product['name']) ?></strong><br>
        Цена: <?= htmlspecialchars($product['price']) ?> MDL<br>
        Категория: <?= htmlspecialchars($product['category']) ?><br>
        Регион: <?= htmlspecialchars($product['region']) ?><br>
        Телефон: <?= htmlspecialchars($product['phone']) ?><br>
        Торг: <?= $product['is_bargain'] ? 'Да' : 'Нет' ?><br>
        <small>Опубликовано: <?= $product['created_at'] ?></small><br><br>
        
        <?php if (!empty($product['images'])): ?>
            <div style="display: flex; gap: 10px;">
                <?php foreach ($product['images'] as $img): ?>
                    <img src="/image.php?img=<?= urlencode(basename($img)) ?>" alt="Фото товара" style="max-width:200px; max-height:150px;">
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <em>Нет изображений</em>
        <?php endif; ?>
        
        <div style="margin-top:10px;">
            <a href="/product/update?id=<?= urlencode($product['id']) ?>" style="margin-right: 10px;">Редактировать</a>
            <a href="/product/delete?id=<?= urlencode($product['id']) ?>"
               onclick="return confirm('Вы действительно хотите удалить этот товар?');">Удалить</a>
        </div>
    </div>
<?php endforeach; ?>

<div style="margin-top:20px;">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">« Назад</a>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="?page=<?= $p ?>" style="<?= $p == $page ? 'font-weight:bold;' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>">Вперёд »</a>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = "Все товары";
include __DIR__ . '/../layout.php';
?>
