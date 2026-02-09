<?php
// helpers/auth.php
declare(strict_types=1);

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(string $base_path): void {
    if (!current_user()) {
        flash_set('warning', 'Please login to continue.');
        redirect(url($base_path, 'login.php'));
    }
}

function admin_user(): ?array {
    return $_SESSION['admin'] ?? null;
}

function require_admin(string $base_path): void {
    if (!admin_user()) {
        redirect(url($base_path, 'admin/login.php'));
    }
}
?>