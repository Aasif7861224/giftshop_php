<?php
require __DIR__ . '/config/bootstrap.php';

// Fetch categories
$catStmt = $pdo->query("SELECT id, name, slug, image FROM categories WHERE status=1 ORDER BY created_at DESC LIMIT 6");
$categories = $catStmt->fetchAll();

// Featured products
$prodStmt = $pdo->query("SELECT p.id,p.name,p.slug,p.price,p.image,c.name AS category
                         FROM products p
                         JOIN categories c ON c.id=p.category_id
                         WHERE p.status=1
                         ORDER BY p.created_at DESC
                         LIMIT 8");
$products = $prodStmt->fetchAll();

include __DIR__ . '/includes/top.php';
?>

<div class="p-4 p-md-5 mb-4 hero">
  <div class="col-lg-7">
    <h1 class="display-6 fw-bold">Send Perfect Gifts, Faster 🎁</h1>
    <p class="lead mb-3">Cakes, flowers, chocolates & gift boxes — all in one place.</p>
    <div class="d-flex gap-2">
      <a class="btn btn-light fw-bold" href="<?= e(url($base_path,'products.php')) ?>">Shop Now</a>
      <a class="btn btn-outline-light fw-bold" href="<?= e(url($base_path,'categories.php')) ?>">Browse Categories</a>
    </div>
  </div>
</div>

<h4 class="mb-3">Popular Categories</h4>
<div class="row g-3 mb-4">
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

<h4 class="mb-3">New Arrivals</h4>
<div class="row g-3">
  <?php foreach ($products as $p): ?>
    <div class="col-6 col-md-3">
      <div class="card card-hover h-100">
        <a href="<?= e(url($base_path,'product.php?slug=' . urlencode($p['slug']))) ?>" class="text-decoration-none text-dark">
          <img class="img-cover" src="<?= e($p['image'] ?: 'images/banner.jpg') ?>" alt="">
          <div class="card-body">
            <div class="small text-muted"><?= e($p['category']) ?></div>
            <div class="fw-semibold"><?= e($p['name']) ?></div>
            <div class="mt-2 fw-bold">₹<?= e(number_format((float)$p['price'], 2)) ?></div>
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

<?php include __DIR__ . '/includes/bottom.php'; ?>
