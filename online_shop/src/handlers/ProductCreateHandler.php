<?php
include '../helpers.php';
function validateProductData($productData) {
  $validationErrorMessages = [];
  global $regions;
  if(!preg_match('/^\+373\d{8}$/',$productData['phone'])) {
    // die("<p>Ошибка: Некорректный формат номера телефона!</p>");
    array_push($validationErrorMessages, "Номер телефона должен быть в формате +373xxxxxxxx");
  }

  if($productData['price'] <= 0) {
    array_push($validationErrorMessages, "Цена не может быть отрицательной или равна 0");
  }

  if (!in_array($productData['region'], $regions)) {
    // die("<p>Ошибка: Некорректный регион!</p>");
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
        //File is not an image.
        array_push($validationErrorMessages, "Файл - $i не является изображением!");
        continue;
      }
      if ($fileSize > $maxFileSize) {
        //File is too large.
        array_push($validationErrorMessages, "Файл - $i слишком большой!");
      }
      if(!in_array($imageFileType, $allowedFormats)) {
        //File is not an image.
        
        array_push($validationErrorMessages, "Файл - $i должен быть в формате jpg, png, jpeg или webp!");
      }
    }
    

    
  }

  return $validationErrorMessages;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Массив, куда будем собирать информацию о новом товаре
  $productData = [
      'id'          => uniqid(),  // Уникальный идентификатор
      'images'      => [],
      'name'        => htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8'),
      'description' => htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8'),
      'price'       => filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT),
      'category'    => htmlspecialchars(trim($_POST['category'] ?? ''), ENT_QUOTES, 'UTF-8'),
      'phone'       => trim($_POST['phone'] ?? ''),
      'region'      => htmlspecialchars(trim($_POST['region'] ?? ''), ENT_QUOTES, 'UTF-8'),
      'is_bargain'  => isset($_POST['is_bargain']) ? true : false,
      'created_at'  => date('Y-m-d H:i:s') // Текущая дата/время
  ];
}


$message = validateProductData($productData);

if (!empty($message)) { 
  echo "<p>" . implode("<br>", $message) . "</p>";
} 


?>