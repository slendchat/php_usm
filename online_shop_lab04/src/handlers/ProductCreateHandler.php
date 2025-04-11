<?php
$productsFile = __DIR__ . '/../../storage/products.json';
include '../helpers.php';

/**
 * product data validation.
 *
 * @param array $productData - array of product data.
 *
 * @return array - array of error messages.
 */
function validateProductData($productData) {
  $validationErrorMessages = [];
  global $regions;
  if(!preg_match('/^\+373\d{8}$/',$productData['phone'])) {
    array_push($validationErrorMessages, "Номер телефона должен быть в формате +373xxxxxxxx");
  }

  if($productData['price'] <= 0) {
    array_push($validationErrorMessages, "Цена не может быть отрицательной или равна 0");
  }

  if (!in_array($productData['region'], $regions)) {
    array_push($validationErrorMessages, "Некорректный регион!");
  }

  if (!empty($_FILES['images']['name'][0])) {

    $filesCount = count($_FILES['images']['name']);
    $maxFiles = min($filesCount, 3);
    $maxFileSize = 10 * 1024 * 1024;
    $allowedFormats = ["jpg", "jpeg", "png", "webp"];


    for ($i = 0; $i < $maxFiles; $i++) {
      $imageFileType = strtolower(pathinfo($_FILES["images"]["name"][$i],PATHINFO_EXTENSION));

      if (!isset($_FILES["images"]["tmp_name"][$i]) || $_FILES["images"]["error"][$i] !== UPLOAD_ERR_OK) {
        array_push($validationErrorMessages, "Ошибка загрузки файла - {$_FILES["images"]["name"][$i]}!");
        continue;
      } 

      $tmpName = $_FILES["images"]["tmp_name"][$i];
      $fileSize = $_FILES["images"]["size"][$i];
      $fileName = $_FILES["images"]["name"][$i];
      $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

      $check = getimagesize($tmpName);
      if($check === false) {
        array_push($validationErrorMessages, "Файл - $i не является изображением!");
        continue;
      }
      if ($fileSize > $maxFileSize) {
        array_push($validationErrorMessages, "Файл - $i слишком большой!");
      }
      if(!in_array($imageFileType, $allowedFormats)) {
        
        array_push($validationErrorMessages, "Файл - $i должен быть в формате jpg, png, jpeg или webp!");
      }
    }
    

    
  }

  return $validationErrorMessages;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
}


$errors = validateProductData($productData);




/**
 * Handles the product creation process, including image uploads and data validation.
 *
 * If there are no validation errors, this code will:
 * - Create an upload directory if it doesn't exist
 * - Limit image uploads to a maximum of 3 files
 * - Generate unique filenames for the uploaded images
 * - Move the uploaded images to the designated upload directory
 * - Append the product data to the existing products
 * - Save all products to the products file in JSON format
 * - Redirect to the main page upon successful operation
 *
 * If an exception occurs during the process, an error message will be displayed.
 * If there are validation errors, they will be displayed to the user.
 */
if(empty($errors)) {
  try {
    $existingProducts = [];
    if (file_exists($productsFile)) {
      $jsonContent = file_get_contents($productsFile);
      $existingProducts = json_decode($jsonContent, true) ?? [];
    }
    if (!empty($_FILES['images']['name'][0])) {
      $uploadDir = __DIR__ . '/../../storage/uploads'; 
      if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);
      }
      $filesCount = count($_FILES['images']['name']);
      $maxFiles = min($filesCount, 3);

      for ($i = 0; $i < $maxFiles; $i++) {
          if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
              $tmpName = $_FILES['images']['tmp_name'][$i];
              $fileName = uniqid() . '_' . basename($_FILES['images']['name'][$i]);

              $destination = $uploadDir . '/' . $fileName;
              if (move_uploaded_file($tmpName, $destination)) {
                  $productData['images'][] = 'uploads/' . $fileName;
              }
          }
      }
    }
    $existingProducts[] = $productData;
    file_put_contents($productsFile, json_encode($existingProducts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: /public/index.php");
    exit;
  } catch (Exception $e) {
    echo "<p>Произошла ошибка: " . $e->getMessage() . "</p>";
  }
}
else {
  echo "<p>" . implode("<br>", $errors) . "</p>";
}

?>
