<?php
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../../helpers.php';
ob_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /product/create");
    exit;
}


$productsFile = __DIR__ . '/../../storage/products.json';


function validateProductData($productData) {
    global $regions;
    $validationErrorMessages = [];

    if (!preg_match('/^\+373\d{8}$/', $productData['phone'])) {
        $validationErrorMessages[] = "Номер телефона должен быть в формате +373xxxxxxxx";
    }

    if ($productData['price'] <= 0) {
        $validationErrorMessages[] = "Цена не может быть отрицательной или равна 0";
    }

    if (!in_array($productData['region'], $regions)) {
        $validationErrorMessages[] = "Некорректный регион!";
    }

    if (!empty($_FILES['images']['name'][0])) {
        $filesCount = count($_FILES['images']['name']);
        $maxFiles = min($filesCount, 3);
        $maxFileSize = 8 * 1024 * 1024; 
        $allowedFormats = ["jpg", "jpeg", "png", "webp"];

        for ($i = 0; $i < $maxFiles; $i++) {
            $fileName = $_FILES["images"]["name"][$i];
            $tmpName = $_FILES["images"]["tmp_name"][$i];
            $fileSize = $_FILES["images"]["size"][$i];
            $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!isset($tmpName) || $_FILES["images"]["error"][$i] !== UPLOAD_ERR_OK) {
                $validationErrorMessages[] = "Ошибка загрузки файла - {$fileName}!";
                continue;
            }

            $check = getimagesize($tmpName);
            if ($check === false) {
                $validationErrorMessages[] = "Файл $i не является изображением!";
                continue;
            }
            if ($fileSize > $maxFileSize) {
                $validationErrorMessages[] = "Файл $i слишком большой!";
            }
            if (!in_array($imageFileType, $allowedFormats)) {
                $validationErrorMessages[] = "Файл $i должен быть в формате jpg, png, jpeg или webp!";
            }
        }
    }

    return $validationErrorMessages;
}

$productData = [
    'id'          => uniqid(),  
    'images'      => [],
    'name'        => htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8'),
    'description' => htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8'),
    'price'       => filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT),
    'category'    => htmlspecialchars(trim($_POST['category'] ?? ''), ENT_QUOTES, 'UTF-8'),
    'phone'       => trim($_POST['phone'] ?? ''),
    'region'      => htmlspecialchars(trim($_POST['region'] ?? ''), ENT_QUOTES, 'UTF-8'),
    'is_bargain'  => isset($_POST['is_bargain']) ? true : false,
    'created_at'  => date('Y-m-d H:i:s') 
];

$errors = validateProductData($productData);

if (empty($errors)) {
    try {
        $images = [];
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = __DIR__ . '/../../../storage/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filesCount = count($_FILES['images']['name']);
            $maxFiles = min($filesCount, 3);

            for ($i = 0; $i < $maxFiles; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['images']['tmp_name'][$i];
                    $uniqueFileName = uniqid() . '_' . basename($_FILES['images']['name'][$i]);

                    $destination = $uploadDir . '/' . $uniqueFileName;
                    if (move_uploaded_file($tmpName, $destination)) {
                        $images[] = 'uploads/' . $uniqueFileName;
                    } else {
                        error_log("Не удалось переместить файл из $tmpName в $destination");
                        die("Ошибка перемещения файла. Проверь права доступа и путь.");
                    }
                }
            }
        }

        $db = new Database();
        $db->insertProduct($productData);
        if (!empty($images)) {
            $db->insertProductImages($productData['id'], $images);
        }
        header("Location: /");
        exit;
    } catch (Exception $e) {
        echo "<p>Произошла ошибка: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>" . implode("<br>", $errors) . "</p>";
}
