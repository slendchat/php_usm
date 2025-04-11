<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';

$db = new Database();

// Проверяем, если запрос POST – обновляем товар
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['id'] ?? '';
    if (!$productId) {
        echo "<p>Нет идентификатора продукта.</p>";
        exit;
    }
    $productData = [
        'name'        => htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'description' => htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'price'       => filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT),
        'category'    => htmlspecialchars(trim($_POST['category'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'phone'       => trim($_POST['phone'] ?? ''),
        'region'      => htmlspecialchars(trim($_POST['region'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'is_bargain'  => isset($_POST['is_bargain']) ? 1 : 0,
    ];

    try {
        $sql = "UPDATE products SET 
                    name = :name,
                    description = :description,
                    price = :price,
                    category = :category,
                    phone = :phone,
                    region = :region,
                    is_bargain = :is_bargain
                WHERE id = :id";
        // Объединяем данные и идентификатор
        $params = array_merge($productData, [':id' => $_POST['id']]);
        $db->query($sql, $params);
        // Дополнительно можно обработать загрузку новых изображений
        header("Location: /product");
        exit;
    } catch (Exception $e) {
        echo "<p>Ошибка при обновлении продукта: " . $e->getMessage() . "</p>";
    }
}