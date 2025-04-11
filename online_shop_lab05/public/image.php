<?php
// public/image.php

// Получаем имя файла из параметра, чтобы избежать попыток обхода (например, с "../")
$filename = basename($_GET['img'] ?? '');

if (empty($filename)) {
    http_response_code(400);
    exit('Не указан файл.');
}

$filepath = __DIR__ . '/../storage/uploads/' . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    exit('Файл не найден.');
}

// Определяем MIME-тип файла
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filepath);
finfo_close($finfo);

// (Можно дополнительно проверить, что MIME-тип входит в список разрешённых, напр.: image/jpeg, image/png, и т.п.)
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(403);
    exit('Доступ запрещён.');
}

// Отдаем заголовки и содержимое файла
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit;
