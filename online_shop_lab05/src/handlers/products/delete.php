/**
 * Handles the deletion of a product based on the provided product ID.
 *
 * This script checks if a product ID is provided via the `id` query parameter.
 * If the ID is not provided, the user is redirected to the product listing page.
 * If the ID is provided, the script attempts to delete the product from the database.
 * Upon successful deletion, the user is redirected to the product listing page.
 * If an error occurs during the deletion process, an error message is displayed.
 *
 * @throws Exception If an error occurs during the deletion process.
 */
<?php

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
