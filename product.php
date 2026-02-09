<?php
require __DIR__ . '/config/bootstrap.php';

$slug = isset($_GET['slug']) ? trim((string)$_GET['slug']) : '';
if ($slug === '') {
    redirect(url($base_path,'products.php'));
}

$stmt = $pdo->prepare("SELECT p.*, c.name AS category, c.slug AS cat_slug
                       FROM products p
                       JOIN categories c ON c.id=p.category_id
                       WHERE p.slug=:slug AND p.status=1
                       LIMIT 1");
$stmt->execute([':slug' => $slug]);
$product = $stmt->fetch();

if (!$product) {
    flash_set('danger', 'Product not found.');
    redirect(url($base_path,'products.php'));
}

include __DIR__ . '/includes/top.php';
?>

<div class="row g-4">
  <div class="col-md-5">
    <div class="card">
      <img src="<?= e($product['image'] ?: 'images/banner.jpg') ?>" class="w-100" style="height:360px;object-fit:cover" alt="">
    </div>
  </div>

  <div class="col-md-7">
    <div class="mb-2 text-muted"><?= e($product['category']) ?></div>
    <h2 class="fw-bold"><?= e($product['name']) ?></h2>
    <div class="fs-4 fw-bold mt-2">₹<?= e(number_format((float)$product['price'], 2)) ?></div>

    <?php if (!empty($product['description'])): ?>
      <p class="mt-3"><?= nl2br(e((string)$product['description'])) ?></p>
    <?php endif; ?>

    <div class="mt-3 d-flex gap-2">
      <form method="post" action="<?= e(url($base_path,'cart_action.php')) ?>" class="d-flex gap-2">
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
        <input type="hidden" name="action" value="add">
        <input type="number" name="qty" min="1" value="1" class="form-control" style="width:120px">
        <button class="btn fw-bold" style="background:#c2185b;color:#fff">Add to Cart</button>
      </form>
      <a class="btn btn-outline-secondary fw-bold" href="<?= e(url($base_path,'cart.php')) ?>">View Cart</a>
    </div>

    <hr class="my-4">
    <div class="small text-muted">Stock: <?= (int)$product['stock'] ?> • Slug: <?= e($product['slug']) ?></div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
