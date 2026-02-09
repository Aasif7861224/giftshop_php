<?php
require __DIR__ . '/config/bootstrap.php';
require_login($base_path);

$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    flash_set('warning','Cart empty.');
    redirect(url($base_path,'products.php'));
}

$user = current_user();
$errors = [];
$full_name = $phone = $address1 = $address2 = $city = $state = $pincode = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim((string)($_POST['full_name'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $address1 = trim((string)($_POST['address1'] ?? ''));
    $address2 = trim((string)($_POST['address2'] ?? ''));
    $city = trim((string)($_POST['city'] ?? ''));
    $state = trim((string)($_POST['state'] ?? ''));
    $pincode = trim((string)($_POST['pincode'] ?? ''));

    if ($full_name === '') $errors[] = 'Full name required';
    if ($phone === '') $errors[] = 'Phone required';
    if ($address1 === '') $errors[] = 'Address required';
    if ($city === '') $errors[] = 'City required';
    if ($state === '') $errors[] = 'State required';
    if ($pincode === '') $errors[] = 'Pincode required';

    if (!$errors) {
        $subtotal = cart_total();
        $shipping = ($subtotal < 500) ? 49 : 0;
        $total = $subtotal + $shipping;

        $order_no = 'PG' . date('ymdHis') . random_int(100, 999);

        try {
            $pdo->beginTransaction();

            $oStmt = $pdo->prepare("INSERT INTO orders
                (user_id, order_no, subtotal, shipping, total, status, full_name, phone, address1, address2, city, state, pincode)
                VALUES (:uid,:ono,:sub,:ship,:tot,'pending',:fn,:ph,:a1,:a2,:city,:st,:pin)");
            $oStmt->execute([
                ':uid'=>$user['id'],
                ':ono'=>$order_no,
                ':sub'=>$subtotal,
                ':ship'=>$shipping,
                ':tot'=>$total,
                ':fn'=>$full_name,
                ':ph'=>$phone,
                ':a1'=>$address1,
                ':a2'=>$address2 ?: null,
                ':city'=>$city,
                ':st'=>$state,
                ':pin'=>$pincode,
            ]);

            $order_id = (int)$pdo->lastInsertId();

            $iStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, price, qty) VALUES (:oid,:pid,:pr,:q)");
            foreach ($cart as $item) {
                $iStmt->execute([
                    ':oid'=>$order_id,
                    ':pid'=>(int)$item['product_id'],
                    ':pr'=>(float)$item['price'],
                    ':q'=>(int)$item['qty'],
                ]);
            }

            // Create payment row (status created)
            $pStmt = $pdo->prepare("INSERT INTO payments (order_id, provider, payment_ref, amount, currency, status)
                                    VALUES (:oid, :prov, :pref, :amt, :cur, 'created')");
            $pStmt->execute([
                ':oid'=>$order_id,
                ':prov'=>$payment_provider === 'razorpay' ? 'razorpay' : 'demo',
                ':pref'=>'INIT-' . $order_no,
                ':amt'=>$total,
                ':cur'=>$currency,
            ]);

            $pdo->commit();

            // Clear cart after order create (so duplicate orders na bane)
            $_SESSION['cart'] = [];

            redirect(url($base_path,'payment.php?order=' . urlencode($order_no)));
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errors[] = 'Order create failed. Try again.';
        }
    }
}

include __DIR__ . '/includes/top.php';
?>

<h3 class="mb-3">Checkout</h3>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold">Shipping Details</h5>

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input class="form-control" name="full_name" value="<?= e($full_name) ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input class="form-control" name="phone" value="<?= e($phone) ?>" required>
            </div>
            <div class="col-12">
              <label class="form-label">Address Line 1</label>
              <input class="form-control" name="address1" value="<?= e($address1) ?>" required>
            </div>
            <div class="col-12">
              <label class="form-label">Address Line 2 (optional)</label>
              <input class="form-control" name="address2" value="<?= e($address2) ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">City</label>
              <input class="form-control" name="city" value="<?= e($city) ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">State</label>
              <input class="form-control" name="state" value="<?= e($state) ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Pincode</label>
              <input class="form-control" name="pincode" value="<?= e($pincode) ?>" required>
            </div>
          </div>

          <button class="btn fw-bold mt-3" style="background:#c2185b;color:#fff">Place Order</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold">Order Summary</h5>
        <div class="small text-muted mb-2">Items: <?= (int)cart_count() ?></div>
        <ul class="list-group mb-3">
          <?php foreach ($cart as $it): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?= e($it['name']) ?> × <?= (int)$it['qty'] ?></span>
              <span>₹<?= e(number_format((float)$it['price']*(int)$it['qty'],2)) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>

        <?php $subtotal = cart_total(); $shipping = ($subtotal < 500) ? 49 : 0; $total = $subtotal + $shipping; ?>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Subtotal</span>
          <span>₹<?= e(number_format($subtotal,2)) ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Shipping</span>
          <span>₹<?= e(number_format($shipping,2)) ?></span>
        </div>
        <hr>
        <div class="d-flex justify-content-between fw-bold">
          <span>Total</span>
          <span>₹<?= e(number_format($total,2)) ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
