<?php
require __DIR__ . '/../config/bootstrap.php';
require_admin($base_path);

$start = isset($_GET['start']) ? (string)$_GET['start'] : '';
$end   = isset($_GET['end']) ? (string)$_GET['end'] : '';

$where = "1=1";
$params = [];
if ($start) { $where .= " AND o.created_at >= :start"; $params[':start'] = $start . " 00:00:00"; }
if ($end)   { $where .= " AND o.created_at <= :end";   $params[':end']   = $end . " 23:59:59"; }

$kpiSql = "SELECT 
             COUNT(*) AS orders,
             SUM(CASE WHEN o.status='paid' THEN 1 ELSE 0 END) AS paid_orders,
             SUM(CASE WHEN o.status='paid' THEN o.total ELSE 0 END) AS revenue
           FROM orders o
           WHERE $where";
$kpiStmt = $pdo->prepare($kpiSql);
$kpiStmt->execute($params);
$kpi = $kpiStmt->fetch() ?: ['orders'=>0,'paid_orders'=>0,'revenue'=>0];

$topSql = "SELECT p.name, SUM(oi.qty) AS qty_sold, SUM(oi.qty * oi.price) AS amount
           FROM order_items oi
           JOIN orders o ON o.id=oi.order_id
           JOIN products p ON p.id=oi.product_id
           WHERE o.status='paid' AND $where
           GROUP BY p.id
           ORDER BY amount DESC
           LIMIT 8";
$topStmt = $pdo->prepare($topSql);
$topStmt->execute($params);
$top = $topStmt->fetchAll();

include __DIR__ . '/includes/top.php';
?>

<h3 class="mb-3">Reports</h3>

<div class="card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Start date</label>
        <input class="form-control" type="date" name="start" value="<?= e($start) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">End date</label>
        <input class="form-control" type="date" name="end" value="<?= e($end) ?>">
      </div>
      <div class="col-md-4">
        <button class="btn btn-dark w-100">Apply</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-6 col-lg-4">
    <div class="card"><div class="card-body">
      <div class="text-muted">Total Orders</div>
      <div class="fs-4 fw-bold"><?= (int)$kpi['orders'] ?></div>
    </div></div>
  </div>
  <div class="col-6 col-lg-4">
    <div class="card"><div class="card-body">
      <div class="text-muted">Paid Orders</div>
      <div class="fs-4 fw-bold"><?= (int)$kpi['paid_orders'] ?></div>
    </div></div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card"><div class="card-body">
      <div class="text-muted">Revenue (Paid)</div>
      <div class="fs-4 fw-bold">₹<?= e(number_format((float)$kpi['revenue'],2)) ?></div>
    </div></div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="fw-bold">Top Products</h5>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Product</th><th class="text-end">Qty Sold</th><th class="text-end">Amount</th></tr></thead>
        <tbody>
          <?php foreach($top as $t): ?>
            <tr>
              <td class="fw-semibold"><?= e($t['name']) ?></td>
              <td class="text-end"><?= (int)$t['qty_sold'] ?></td>
              <td class="text-end">₹<?= e(number_format((float)$t['amount'],2)) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if(!$top): ?><tr><td colspan="3" class="text-muted">No paid data yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
