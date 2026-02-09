<?php
require __DIR__ . '/../config/bootstrap.php';
require_admin($base_path);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $status = (string)($_POST['status'] ?? 'pending');
    $allowed = ['pending','paid','shipped','delivered','cancelled'];
    if ($id > 0 && in_array($status, $allowed, true)) {
        $pdo->prepare("UPDATE orders SET status=:s WHERE id=:id")->execute([':s'=>$status,':id'=>$id]);
        flash_set('success','Order status updated.');
    }
    redirect(url($base_path,'admin/orders.php'));
}

$orders = $pdo->query("SELECT o.id,o.order_no,o.total,o.status,o.created_at,u.name AS user_name,u.email
                       FROM orders o JOIN users u ON u.id=o.user_id
                       ORDER BY o.created_at DESC")->fetchAll();

include __DIR__ . '/includes/top.php';
?>

<h3 class="mb-3">Orders</h3>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Order</th>
            <th>User</th>
            <th>Total</th>
            <th>Status</th>
            <th>Created</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($orders as $o): ?>
            <tr>
              <td class="fw-semibold"><?= e($o['order_no']) ?></td>
              <td>
                <div class="fw-semibold"><?= e($o['user_name']) ?></div>
                <div class="small text-muted"><?= e($o['email']) ?></div>
              </td>
              <td>₹<?= e(number_format((float)$o['total'],2)) ?></td>
              <td><span class="badge text-bg-secondary"><?= e($o['status']) ?></span></td>
              <td class="text-muted small"><?= e($o['created_at']) ?></td>
              <td class="text-end">
                <form method="post" class="d-flex gap-2 justify-content-end">
                  <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                  <select class="form-select form-select-sm" name="status" style="width:160px">
                    <?php foreach(['pending','paid','shipped','delivered','cancelled'] as $s): ?>
                      <option value="<?= e($s) ?>" <?= $o['status']===$s?'selected':'' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn btn-sm btn-dark">Update</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if(!$orders): ?><tr><td colspan="6" class="text-muted">No orders.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
