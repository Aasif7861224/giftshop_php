<?php
require __DIR__ . '/config/bootstrap.php';
require_login($base_path);

$user = current_user();
$order_no = isset($_GET['order']) ? trim((string)$_GET['order']) : '';
if ($order_no === '') {
    redirect(url($base_path,'orders.php'));
}

$oStmt = $pdo->prepare("SELECT * FROM orders WHERE order_no=:ono AND user_id=:uid LIMIT 1");
$oStmt->execute([':ono'=>$order_no,':uid'=>$user['id']]);
$order = $oStmt->fetch();

if (!$order) {
    flash_set('danger','Order not found.');
    redirect(url($base_path,'orders.php'));
}

$payStmt = $pdo->prepare("SELECT * FROM payments WHERE order_id=:oid ORDER BY id DESC LIMIT 1");
$payStmt->execute([':oid'=>$order['id']]);
$payment = $payStmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $order['status'] === 'pending') {
    // DEMO payment success
    $ref = 'DEMO-' . strtoupper(bin2hex(random_bytes(4))) . '-' . $order_no;

    $pdo->beginTransaction();
    try {
        $u1 = $pdo->prepare("UPDATE payments SET payment_ref=:ref, status='paid', paid_at=NOW(), raw_response=:raw WHERE id=:pid");
        $raw = json_encode(['mode'=>'demo','ref'=>$ref,'order_no'=>$order_no,'amount'=>$order['total']], JSON_UNESCAPED_SLASHES);
        $u1->execute([':ref'=>$ref,':raw'=>$raw,':pid'=>$payment['id']]);

        $u2 = $pdo->prepare("UPDATE orders SET status='paid' WHERE id=:oid");
        $u2->execute([':oid'=>$order['id']]);

        $pdo->commit();

        flash_set('success','Payment successful (Demo).');
        redirect(url($base_path,'order_view.php?id='.(int)$order['id']));
    } catch (Throwable $e) {
        $pdo->rollBack();
        flash_set('danger','Payment failed.');
        redirect(url($base_path,'payment.php?order=' . urlencode($order_no)));
    }
}

include __DIR__ . '/includes/top.php';
?>

<h3 class="mb-3">Payment</h3>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold">Order: <?= e($order['order_no']) ?></h5>
        <div class="text-muted">Amount: <span class="fw-bold">₹<?= e(number_format((float)$order['total'],2)) ?></span></div>
        <div class="mt-2">
          <span class="badge text-bg-<?= $order['status']==='paid'?'success':'secondary' ?>"><?= e($order['status']) ?></span>
        </div>

        <hr>

        <?php if ($order['status'] === 'paid'): ?>
          <div class="alert alert-success">Already paid.</div>
          <a class="btn btn-outline-primary" href="<?= e(url($base_path,'order_view.php?id='.(int)$order['id'])) ?>">View Order</a>
        <?php else: ?>
          <div class="alert alert-info">
            <div class="fw-bold">Demo Payment System</div>
            <div class="small">Major project ke liye payment flow show karne ke liye ye demo gateway hai.</div>
          </div>

          <form method="post">
            <div class="mb-2">
              <label class="form-label">Choose Method</label>
              <select class="form-select" disabled>
                <option selected>Demo Card / UPI (Simulation)</option>
              </select>
            </div>
            <button class="btn fw-bold w-100" style="background:#c2185b;color:#fff">Pay Now (Demo Success)</button>
          </form>

          <div class="small text-muted mt-3">
            Note: Real Razorpay test mode later add kar sakte ho (keys config.php me).
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card">
      <div class="card-body">
        <h6 class="fw-bold">Shipping</h6>
        <div class="small text-muted"><?= e($order['full_name']) ?> • <?= e($order['phone']) ?></div>
        <div><?= e($order['address1']) ?></div>
        <?php if (!empty($order['address2'])): ?><div><?= e($order['address2']) ?></div><?php endif; ?>
        <div><?= e($order['city']) ?>, <?= e($order['state']) ?> - <?= e($order['pincode']) ?></div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
