<?php
require __DIR__ . '/../config/bootstrap.php';
require_admin($base_path);

$counts = [
  'users' => (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
  'categories' => (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
  'products' => (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
  'orders' => (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
  'paid_orders' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='paid'")->fetchColumn(),
];

include __DIR__ . '/includes/top.php';
?>
<h3 class="mb-3">Dashboard</h3>

<div class="row g-3">
  <div class="col-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted">Users</div><div class="fs-4 fw-bold"><?= $counts['users'] ?></div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted">Categories</div><div class="fs-4 fw-bold"><?= $counts['categories'] ?></div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted">Products</div><div class="fs-4 fw-bold"><?= $counts['products'] ?></div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted">Orders</div><div class="fs-4 fw-bold"><?= $counts['orders'] ?></div></div></div></div>
</div>

<div class="row g-3 mt-1">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold">Recent Orders</h5>
        <?php
          $recent = $pdo->query("SELECT order_no,total,status,created_at FROM orders ORDER BY created_at DESC LIMIT 8")->fetchAll();
        ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Order</th><th>Total</th><th>Status</th><th>Time</th></tr></thead>
            <tbody>
              <?php foreach($recent as $r): ?>
                <tr>
                  <td class="fw-semibold"><?= e($r['order_no']) ?></td>
                  <td>₹<?= e(number_format((float)$r['total'],2)) ?></td>
                  <td><span class="badge text-bg-secondary"><?= e($r['status']) ?></span></td>
                  <td class="text-muted small"><?= e($r['created_at']) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if(!$recent): ?><tr><td colspan="4" class="text-muted">No orders yet.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold">Quick Links</h5>
        <div class="d-grid gap-2">
          <a class="btn btn-outline-dark" href="<?= e(url($base_path,'admin/categories.php')) ?>">Manage Categories</a>
          <a class="btn btn-outline-dark" href="<?= e(url($base_path,'admin/products.php')) ?>">Manage Products</a>
          <a class="btn btn-outline-dark" href="<?= e(url($base_path,'admin/orders.php')) ?>">Manage Orders</a>
          <a class="btn btn-outline-dark" href="<?= e(url($base_path,'admin/reports.php')) ?>">View Reports</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
