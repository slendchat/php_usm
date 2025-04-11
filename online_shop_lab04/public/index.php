<?php

$productsFile = __DIR__ . '/../storage/products.json';

// loading products
$products = [];
if (file_exists($productsFile)) {
    $jsonContent = file_get_contents($productsFile);
    $products = json_decode($jsonContent, true) ?? [];
}

usort($products, function ($a, $b) {
    return strtotime($b['created_at']) <=> strtotime($a['created_at']);
});

$latestProducts = array_slice($products, 0, 2);
?>

<h1>Последние товары</h1>
<?php foreach ($latestProducts as $product): ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <strong><?= htmlspecialchars($product['name']) ?></strong><br>
        Цена: <?= htmlspecialchars($product['price']) ?> MDL<br>
        Регион: <?= htmlspecialchars($product['region']) ?><br>
        <small>Опубликовано: <?= $product['created_at'] ?></small><br><br>

        <?php if (!empty($product['images'])): ?>
            <?php foreach ($product['images'] as $img): ?>
                <img src="/storage/<?= htmlspecialchars($img) ?>" alt="Фото товара" style="max-width: 200px; max-height: 150px; margin-right: 10px;">
            <?php endforeach; ?>
        <?php else: ?>
            <em>Нет изображений</em>
        <?php endif; ?>
    </div>
<?php endforeach; ?>


