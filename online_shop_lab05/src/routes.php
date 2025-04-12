<?php
/**
 * This script handles routing for the application based on the requested URI.
 * It parses the URI, trims trailing slashes, and maps it to the appropriate
 * template or handler file. If the URI does not match any predefined routes,
 * a 404 error is returned.
 *
 * Routes:
 * - `/` or `/index`: Loads the main index template.
 * - `/product`: Loads the product index template.
 * - `/product/create`: 
 *     - GET: Loads the product creation template.
 *     - POST: Processes product creation via the handler.
 * - `/product/update`: 
 *     - GET: Loads the product update template.
 *     - POST: Processes product update via the handler.
 * - `/product/delete`: Processes product deletion via the handler.
 * 
 * If the URI does not match any of the above routes, a 404 error page is displayed.
 */
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
