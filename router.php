<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route /home to cliente/home.php
if ($uri === '/home' || $uri === '/home/') {
    require __DIR__ . '/cliente/home.php';
    return true;
}

// Let PHP's built-in server handle everything else from cliente/
return false;
