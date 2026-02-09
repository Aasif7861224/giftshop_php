<?php
require __DIR__ . '/../config/bootstrap.php';
require_admin($base_path);

$errors = [];
$name = $price = $stock = $image = $description = '';
$category_id = 0;

$cats = $pdo->query("SELECT id,name FROM categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)($_POST['category_id'] ?? 0);
    $name = trim((string)($_POST['name'] ?? ''));
    $price = trim((string)($_POST['price'] ?? '0'));
    $stock = (int)($_POST['stock'] ?? 0);
    $image = trim((string)($_POST['image'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));

    if ($category_id <= 0) $errors[] = 'Category required';
    if ($name === '') $errors[] = 'Name required';
    if (!is_numeric($price) || (float)$price < 0) $errors[] = 'Valid price required';

    if (!$errors) {
        $slug = slugify($name);

        $exists = $pdo->prepare("SELECT id FROM products WHERE slug=:s LIMIT 1");
        $exists->execute([':s'=>$slug]);
        if ($exists->fetch()) {
            $slug .= '-' . random_int(10, 99);
        }

        $ins = $pdo->prepare("INSERT INTO products (category_id,name,slug,description,price,stock,image,status)
                              VALUES (:cid,:n,:s,:d,:p,:st,:img,1)");
        $ins->execute([
          ':cid'=>$category_id, ':n'=>$name, ':s'=>$slug, ':d'=>$description ?: null,
          ':p'=>(float)$price, ':st'=>$stock, ':img'=>$image ?: null
        ]);
        flash_set('success','Product added.');
        redirect(url($base_path,'admin/products.php'));
    }
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE products SET status = IF(status=1,0,1) WHERE id=:id")->execute([':id'=>$id]);
    redirect(url($base_path,'admin/products.php'));
}

$products = $pdo->query("SELECT p.id,p.name,p.slug,p.price,p.stock,p.status,p.created_at,c.name AS category
                         FROM products p JOIN categories c ON c.id=p.category_id
                         ORDER BY p.created_at DESC")->fetchAll();

include __DIR__ . '/includes/top.php';
?>

<h3 class="mb-3">Products</h3>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold">Add Product</h5>

        <?php if ($errors): ?>
          <div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e1): ?><li><?= e($e1) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-2">
            <label class="form-label">Category</label>
            <select class="form-select" name="category_id" required>
              <option value="">Select</option>
              <?php foreach($cats as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= $category_id==(int)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">Name</label>
            <input class="form-control" name="name" value="<?= e($name) ?>" required>
          </div>

          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Price</label>
              <input class="form-control" name="price" value="<?= e($price) ?>" required>
            </div>
            <div class="col-6">
              <label class="form-label">Stock</label>
              <input class="form-control" name="stock" type="number" value="<?= (int)$stock ?>" required>
            </div>
          </div>

          <div class="mt-2">
            <label class="form-label">Image path (optional)</label>
            <input class="form-control" name="image" value="<?= e($image) ?>" placeholder="images/cake.jpg">
          </div>

          <div class="mt-2">
            <label class="form-label">Description (optional)</label>
            <textarea class="form-control" name="description" rows="3"><?= e($description) ?></textarea>
          </div>

          <button class="btn btn-dark fw-bold w-100 mt-3">Save</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold">All Products</h5>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Created</th><th></th></tr></thead>
            <tbody>
              <?php foreach($products as $p): ?>
                <tr>
                  <td class="fw-semibold"><?= e($p['name']) ?></td>
                  <td class="text-muted"><?= e($p['category']) ?></td>
                  <td>₹<?= e(number_format((float)$p['price'],2)) ?></td>
                  <td><?= (int)$p['stock'] ?></td>
                  <td><span class="badge text-bg-<?= $p['status']? 'success':'secondary' ?>"><?= $p['status']? 'active':'inactive' ?></span></td>
                  <td class="text-muted small"><?= e($p['created_at']) ?></td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-dark" href="<?= e(url($base_path,'admin/products.php?toggle='.(int)$p['id'])) ?>">Toggle</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if(!$products): ?><tr><td colspan="7" class="text-muted">No products.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
