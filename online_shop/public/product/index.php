<?php
$productsFile = __DIR__ . '/../../storage/products.json';

// Load products from JSON file
$products = [];
if (file_exists($productsFile)) {
    $jsonContent = file_get_contents($productsFile);
    $products = json_decode($jsonContent, true) ?? [];
}

// Sort products by date (newest first)
usort($products, function ($a, $b) {
    return strtotime($b['created_at']) <=> strtotime($a['created_at']);
});

// Pagination parameters
$perPage = 5;
$totalProducts = count($products);
$totalPages = ceil($totalProducts / $perPage);

// Get page number from GET
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$page = max(1, min($page, $totalPages)); // Limit page number to [1, totalPages]

$offset = ($page - 1) * $perPage;
$paginatedProducts = array_slice($products, $offset, $perPage);
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
                    <img src="/storage/<?= htmlspecialchars($img) ?>" alt="Фото товара" style="max-width: 200px; max-height: 150px;">
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <em>Нет изображений</em>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<!-- Navigation -->
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

