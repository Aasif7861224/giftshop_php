<?php
require __DIR__ . '/config/bootstrap.php';
require_login($base_path);

$user = current_user();

$stmt = $pdo->prepare("SELECT id,order_no,total,status,created_at
                       FROM orders WHERE user_id=:uid ORDER BY created_at DESC");
$stmt->execute([':uid' => $user['id']]);
$orders = $stmt->fetchAll();

include __DIR__ . '/includes/top.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">My Orders</h3>
  <a class="btn btn-outline-secondary btn-sm" href="<?= e(url($base_path,'products.php')) ?>">Shop</a>
</div>

<?php if (!$orders): ?>
  <div class="alert alert-info">No orders yet.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table bg-white align-middle">
      <thead>
        <tr>
          <th>Order No</th>
          <th>Total</th>
          <th>Status</th>
          <th>Created</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td class="fw-semibold"><?= e($o['order_no']) ?></td>
            <td>₹<?= e(number_format((float)$o['total'],2)) ?></td>
            <td><span class="badge text-bg-secondary"><?= e($o['status']) ?></span></td>
            <td><?= e($o['created_at']) ?></td>
            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= e(url($base_path,'order_view.php?id='.(int)$o['id'])) ?>">View</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/bottom.php'; ?>
