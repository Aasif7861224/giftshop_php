<?php
require __DIR__ . '/../config/bootstrap.php';
require_admin($base_path);

$errors = [];
$name = $image = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $image = trim((string)($_POST['image'] ?? ''));

    if ($name === '') $errors[] = 'Name required';

    if (!$errors) {
        $slug = slugify($name);

        // Ensure unique slug
        $exists = $pdo->prepare("SELECT id FROM categories WHERE slug=:s LIMIT 1");
        $exists->execute([':s'=>$slug]);
        if ($exists->fetch()) {
            $slug .= '-' . random_int(10, 99);
        }

        $ins = $pdo->prepare("INSERT INTO categories (name,slug,image,status) VALUES (:n,:s,:i,1)");
        $ins->execute([':n'=>$name,':s'=>$slug,':i'=>$image ?: null]);
        flash_set('success','Category added.');
        redirect(url($base_path,'admin/categories.php'));
    }
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE categories SET status = IF(status=1,0,1) WHERE id=:id")->execute([':id'=>$id]);
    redirect(url($base_path,'admin/categories.php'));
}

$cats = $pdo->query("SELECT id,name,slug,status,created_at FROM categories ORDER BY created_at DESC")->fetchAll();

include __DIR__ . '/includes/top.php';
?>

<h3 class="mb-3">Categories</h3>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold">Add Category</h5>

        <?php if ($errors): ?>
          <div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e1): ?><li><?= e($e1) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-2">
            <label class="form-label">Name</label>
            <input class="form-control" name="name" value="<?= e($name) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Image path (optional)</label>
            <input class="form-control" name="image" value="<?= e($image) ?>" placeholder="images/cake.jpg">
            <div class="form-text">Simple approach for major project demo.</div>
          </div>
          <button class="btn btn-dark fw-bold w-100">Save</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="fw-bold">All Categories</h5>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Name</th><th>Slug</th><th>Status</th><th>Created</th><th></th></tr></thead>
            <tbody>
              <?php foreach($cats as $c): ?>
                <tr>
                  <td class="fw-semibold"><?= e($c['name']) ?></td>
                  <td class="text-muted"><?= e($c['slug']) ?></td>
                  <td>
                    <span class="badge text-bg-<?= $c['status']? 'success':'secondary' ?>"><?= $c['status']? 'active':'inactive' ?></span>
                  </td>
                  <td class="text-muted small"><?= e($c['created_at']) ?></td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-dark" href="<?= e(url($base_path,'admin/categories.php?toggle='.(int)$c['id'])) ?>">
                      Toggle
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if(!$cats): ?><tr><td colspan="5" class="text-muted">No categories.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
