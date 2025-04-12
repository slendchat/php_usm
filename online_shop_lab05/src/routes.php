<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/';
}

switch ($uri) {
    case '/':
    case '/index':
        require __DIR__ . '/../templates/index.php';
        break;
    case '/product':
        require __DIR__ . '/../templates/product/index.php';
        break;
    case '/product/create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require __DIR__ . '/../src/handlers/products/create.php';
        } else {
            require __DIR__ . '/../templates/product/create.php';
        }
        break;
    case '/product/update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require __DIR__ . '/../src/handlers/products/update.php';
        } else {
            require __DIR__ . '/../templates/product/update.php';
        }
        break;
    case '/product/delete':
        require __DIR__ . '/../src/handlers/products/delete.php';
        break;
    default:
        http_response_code(404);
        echo "<h1>404 — страница не найдена</h1>";
}
