<?php
// config/bootstrap.php
declare(strict_types=1);

$config = require __DIR__ . '/config.php';

date_default_timezone_set($config['app']['timezone']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic helpers
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../helpers/auth.php';

// DB (PDO)
$pdo = db_connect($config);

// Common globals
$app_name = $config['app']['name'];
$base_path = rtrim($config['app']['base_path'], '/');
$payment_provider = $config['payment']['provider'];
$currency = $config['payment']['currency'];

// Flash messages (one-time)
$flash = flash_get();
?>