<?php
require __DIR__ . '/config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(url($base_path,'cart.php'));
}

$action = isset($_POST['action']) ? (string)$_POST['action'] : '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;

$_SESSION['cart'] = $_SESSION['cart'] ?? [];

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    flash_set('success', 'Cart cleared.');
    redirect(url($base_path,'cart.php'));
}

if ($product_id <= 0) {
    flash_set('danger', 'Invalid product.');
    redirect(url($base_path,'cart.php'));
}

if ($action === 'remove') {
    unset($_SESSION['cart'][$product_id]);
    flash_set('success', 'Item removed.');
    redirect(url($base_path,'cart.php'));
}

if ($action === 'update') {
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['qty'] = $qty;
        flash_set('success', 'Cart updated.');
    }
    redirect(url($base_path,'cart.php'));
}

if ($action === 'add') {
    $stmt = $pdo->prepare("SELECT id,name,price,image FROM products WHERE id=:id AND status=1 LIMIT 1");
    $stmt->execute([':id' => $product_id]);
    $p = $stmt->fetch();
    if (!$p) {
        flash_set('danger', 'Product not found.');
        redirect(url($base_path,'products.php'));
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = [
            'product_id' => (int)$p['id'],
            'name' => (string)$p['name'],
            'price' => (float)$p['price'],
            'image' => (string)($p['image'] ?? ''),
            'qty' => $qty,
        ];
    }

    flash_set('success', 'Added to cart.');
    $back = $_SERVER['HTTP_REFERER'] ?? url($base_path,'cart.php');
    redirect($back);
}

flash_set('danger', 'Unknown action.');
redirect(url($base_path,'cart.php'));
