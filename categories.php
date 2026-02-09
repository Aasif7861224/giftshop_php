<?php
require __DIR__ . '/config/bootstrap.php';

$stmt = $pdo->query("SELECT id,name,slug,image FROM categories WHERE status=1 ORDER BY name");
$categories = $stmt->fetchAll();

include __DIR__ . '/includes/top.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Categories</h3>
  <a class="btn btn-outline-secondary btn-sm" href="<?= e(url($base_path,'products.php')) ?>">View All Products</a>
</div>

<div class="row g-3">
  <?php foreach ($categories as $cat): ?>
    <div class="col-6 col-md-3">
      <a class="text-decoration-none" href="<?= e(url($base_path,'products.php?cat=' . urlencode($cat['slug']))) ?>">
        <div class="card card-hover h-100">
          <img class="img-cover" src="<?= e($cat['image'] ?: 'images/banner.jpg') ?>" alt="">
          <div class="card-body">
            <div class="fw-semibold"><?= e($cat['name']) ?></div>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
