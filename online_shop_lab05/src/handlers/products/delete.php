<?php
// src/handlers/products/delete.php

// Проверяем, что параметр id передан
if (!isset($_GET['id'])) {
    header("Location: /product");
    exit;
}

$productId = $_GET['id'];

require_once __DIR__ . '/../../db.php';

$db = new Database();

try {
    $db->deleteProduct($productId);
    header("Location: /product");
    exit;
} catch (Exception $e) {
    echo "<p>Произошла ошибка при удалении товара: " . $e->getMessage() . "</p>";
}
