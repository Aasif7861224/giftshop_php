<?php
// helpers/functions.php
declare(strict_types=1);

function db_connect(array $config): PDO {
    $db = $config['db'];
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset']);

    try {
        $pdo = new PDO($dsn, $db['user'], $db['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        die('Database connection failed.');
    }
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function url(string $base_path, string $path): string {
    $path = '/' . ltrim($path, '/');
    return $base_path . $path;
}

function flash_set(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function csrf_token(string $secret): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return hash_hmac('sha256', $_SESSION['_csrf'], $secret);
}

function csrf_validate(string $secret, ?string $token): bool {
    if (!$token) return false;
    $expected = csrf_token($secret);
    return hash_equals($expected, $token);
}

function cart_count(): int {
    return isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'qty')) : 0;
}

function cart_total(): float {
    $total = 0.0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += ((float)$item['price']) * ((int)$item['qty']);
        }
    }
    return $total;
}

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return $text ?: 'n-a';
}

?>