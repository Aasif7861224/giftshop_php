<?php
// includes/top.php
// requires: $app_name, $base_path, $flash
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($app_name) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= e(url($base_path,'assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body class="bg-light">
<?php include __DIR__ . '/navbar.php'; ?>

<div class="container py-4">

<?php if ($flash): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast align-items-center text-bg-<?= e($flash['type']) ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body"><?= e($flash['message']) ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
<?php endif; ?>
