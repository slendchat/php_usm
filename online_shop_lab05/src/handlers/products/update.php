/**
 * Handles the update of a product in the database.
 * 
 * This script processes a POST request to update a product's details, including
 * its name, description, price, category, phone, region, and bargain status. It also
 * manages the upload and replacement of associated product images.
 * 
 * Key Features:
 * - Validates and sanitizes input data.
 * - Updates product details in the database.
 * - Deletes old images and uploads new ones (up to 3 images).
 * - Handles errors during file operations and database queries.
 * 
 * Requirements:
 * - Requires the `db.php` file for database operations.
 * - Requires the `helpers.php` file for additional helper functions.
 * 
 * POST Parameters:
 * - `id` (string): The ID of the product to update (required).
 * - `name` (string): The name of the product.
 * - `description` (string): The description of the product.
 * - `price` (float): The price of the product.
 * - `category` (string): The category of the product.
 * - `phone` (string): The contact phone number.
 * - `region` (string): The region of the product.
 * - `is_bargain` (boolean): Whether the product is negotiable (optional).
 * - `images` (array): An array of uploaded image files (optional, max 3).
 * 
 * Error Handling:
 * - Logs errors for file operations (e.g., file deletion or upload failures).
 * - Displays error messages for invalid requests or database issues.
 * 
 * Redirects:
 * - On success, redirects to the `/product` page.
 * 
 * Example Usage:
 * - Send a POST request with the required parameters to update a product.
 */
<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../helpers.php';

$db = new Database();

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
        $params = array_merge($productData, [':id' => $_POST['id']]);
        $db->query($sql, $params);
        
        if (!empty($_FILES['images']['name'][0])) {
            $stmtOld = $db->query("SELECT image_path FROM product_images WHERE product_id = :id", [':id' => $_POST['id']]);
            $oldImages = $stmtOld->fetchAll(PDO::FETCH_COLUMN);
            foreach ($oldImages as $oldImg) {
                $filePath = __DIR__ . '/../../../storage/' . $oldImg;
                if (file_exists($filePath)) {
                    if (!unlink($filePath)) {
                        error_log("Ошибка удаления старого файла: $filePath");
                    }
                }
            }
            $db->query("DELETE FROM product_images WHERE product_id = :id", [':id' => $_POST['id']]);
    
            $uploadDir = __DIR__ . '/../../../storage/uploads';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    error_log("Не удалось создать директорию: $uploadDir");
                    die("Ошибка создания директории для загрузки файлов.");
                }
            }
            
            $filesCount = count($_FILES['images']['name']);
            $maxFiles = min($filesCount, 3);
            $newImages = [];
            
            for ($i = 0; $i < $maxFiles; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['images']['tmp_name'][$i];
                    $originalName = basename($_FILES['images']['name'][$i]);
                    $uniqueFileName = uniqid() . '_' . $originalName;
                    $destination = $uploadDir . '/' . $uniqueFileName;
                    
                    if (move_uploaded_file($tmpName, $destination)) {
                        $newImages[] = 'uploads/' . $uniqueFileName;
                        error_log("Файл успешно перемещён: $destination");
                    } else {
                        error_log("Ошибка: Не удалось переместить файл из $tmpName в $destination");
                    }
                } else {
                    error_log("Ошибка загрузки файла номер $i: код ошибки " . $_FILES['images']['error'][$i]);
                }
            }
            
            if (!empty($newImages)) {
                $db->insertProductImages($_POST['id'], $newImages);
            } else {
                error_log("Новые изображения не были загружены.");
            }
        }
    
        header("Location: /product");
        exit;
    } catch (Exception $e) {
        echo "<p>Ошибка при обновлении продукта: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Неверный метод запроса</p>";
}
