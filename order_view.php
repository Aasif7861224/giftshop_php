<?php
require __DIR__ . '/config/bootstrap.php';
require_login($base_path);

$user = current_user();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=:id AND user_id=:uid LIMIT 1");
$stmt->execute([':id'=>$id,':uid'=>$user['id']]);
$order = $stmt->fetch();

if (!$order) {
    flash_set('danger','Order not found.');
    redirect(url($base_path,'orders.php'));
}

$itemStmt = $pdo->prepare("SELECT oi.qty,oi.price,p.name,p.image
                           FROM order_items oi
                           JOIN products p ON p.id=oi.product_id
                           WHERE oi.order_id=:oid");
$itemStmt->execute([':oid'=>$order['id']]);
$items = $itemStmt->fetchAll();

$payStmt = $pdo->prepare("SELECT provider,payment_ref,status,paid_at FROM payments WHERE order_id=:oid ORDER BY id DESC LIMIT 1");
$payStmt->execute([':oid'=>$order['id']]);
$payment = $payStmt->fetch();

include __DIR__ . '/includes/top.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Order <?= e($order['order_no']) ?></h3>
  <a class="btn btn-outline-secondary btn-sm" href="<?= e(url($base_path,'orders.php')) ?>">Back</a>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold">Items</h5>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th>Product</th>
                <th class="text-end">Price</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <?php $sub = (float)$it['price']*(int)$it['qty']; ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <img src="<?= e($it['image'] ?: 'images/banner.jpg') ?>" style="width:52px;height:52px;object-fit:cover;border-radius:.5rem" alt="">
                      <div class="fw-semibold"><?= e($it['name']) ?></div>
                    </div>
                  </td>
                  <td class="text-end">₹<?= e(number_format((float)$it['price'],2)) ?></td>
                  <td class="text-end"><?= (int)$it['qty'] ?></td>
                  <td class="text-end fw-semibold">₹<?= e(number_format($sub,2)) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <hr>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Total</span>
          <span class="fw-bold">₹<?= e(number_format((float)$order['total'],2)) ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-body">
        <h6 class="fw-bold">Status</h6>
        <div class="badge text-bg-secondary"><?= e($order['status']) ?></div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <h6 class="fw-bold">Shipping</h6>
        <div class="small text-muted"><?= e($order['full_name']) ?> • <?= e($order['phone']) ?></div>
        <div><?= e($order['address1']) ?></div>
        <?php if (!empty($order['address2'])): ?><div><?= e($order['address2']) ?></div><?php endif; ?>
        <div><?= e($order['city']) ?>, <?= e($order['state']) ?> - <?= e($order['pincode']) ?></div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h6 class="fw-bold">Payment</h6>
        <?php if ($payment): ?>
          <div class="small text-muted">Provider: <?= e($payment['provider']) ?></div>
          <div class="small text-muted">Ref: <?= e($payment['payment_ref']) ?></div>
          <div class="badge text-bg-<?= $payment['status']==='paid'?'success':'secondary' ?>"><?= e($payment['status']) ?></div>
        <?php else: ?>
          <div class="text-muted">Not paid yet.</div>
        <?php endif; ?>

        <?php if ($order['status'] === 'pending'): ?>
          <a class="btn btn-sm fw-bold w-100 mt-3" style="background:#c2185b;color:#fff"
             href="<?= e(url($base_path,'payment.php?order=' . urlencode($order['order_no']))) ?>">
            Pay Now
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
