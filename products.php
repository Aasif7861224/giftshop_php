<?php
require __DIR__ . '/config/bootstrap.php';

$catSlug = isset($_GET['cat']) ? trim((string)$_GET['cat']) : '';

$params = [];
$where = "WHERE p.status=1";
if ($catSlug !== '') {
    $where .= " AND c.slug = :slug";
    $params[':slug'] = $catSlug;
}

$sql = "SELECT p.id,p.name,p.slug,p.price,p.image,p.description,c.name AS category,c.slug AS cat_slug
        FROM products p
        JOIN categories c ON c.id=p.category_id
        $where
        ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$cats = $pdo->query("SELECT name,slug FROM categories WHERE status=1 ORDER BY name")->fetchAll();

include __DIR__ . '/includes/top.php';
?>

<div class="row">
  <div class="col-lg-3 mb-3">
    <div class="card">
      <div class="card-header fw-semibold">Filter</div>
      <div class="list-group list-group-flush">
        <a class="list-group-item <?= $catSlug===''?'active':'' ?>" href="<?= e(url($base_path,'products.php')) ?>">All</a>
        <?php foreach ($cats as $c): ?>
          <a class="list-group-item <?= ($catSlug===$c['slug'])?'active':'' ?>"
             href="<?= e(url($base_path,'products.php?cat=' . urlencode($c['slug']))) ?>">
            <?= e($c['name']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-9">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Products <?= $catSlug ? '<span class="text-muted fs-6">(' . e($catSlug) . ')</span>' : '' ?></h3>
      <a class="btn btn-outline-secondary btn-sm" href="<?= e(url($base_path,'cart.php')) ?>">Go to Cart</a>
    </div>

    <div class="row g-3">
      <?php if (!$products): ?>
        <div class="col-12"><div class="alert alert-warning">No products found.</div></div>
      <?php endif; ?>

      <?php foreach ($products as $p): ?>
        <div class="col-6 col-md-4">
          <div class="card card-hover h-100">
            <a href="<?= e(url($base_path,'product.php?slug=' . urlencode($p['slug']))) ?>" class="text-decoration-none text-dark">
              <img class="img-cover" src="<?= e($p['image'] ?: 'images/banner.jpg') ?>" alt="">
              <div class="card-body">
                <div class="small text-muted"><?= e($p['category']) ?></div>
                <div class="fw-semibold"><?= e($p['name']) ?></div>
                <div class="mt-2 fw-bold">₹<?= e(number_format((float)$p['price'],2)) ?></div>
              </div>
            </a>
            <div class="card-footer bg-white border-0">
              <form method="post" action="<?= e(url($base_path,'cart_action.php')) ?>">
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <input type="hidden" name="action" value="add">
                <button class="btn btn-sm w-100" style="background:#c2185b;color:#fff">Add to Cart</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
