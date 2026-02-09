<?php
// admin/includes/top.php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin • <?= e($app_name) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= e(url($base_path,'admin/index.php')) ?>">Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdmin">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navAdmin">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?= e(url($base_path,'admin/categories.php')) ?>">Categories</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= e(url($base_path,'admin/products.php')) ?>">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= e(url($base_path,'admin/orders.php')) ?>">Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= e(url($base_path,'admin/reports.php')) ?>">Reports</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= e(url($base_path,'admin/logout.php')) ?>">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
