<?php
$user = current_user();
$cartCount = cart_count();
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#c2185b">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= e(url($base_path,'index.php')) ?>"><?= e($app_name) ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?= e(url($base_path,'categories.php')) ?>">Categories</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= e(url($base_path,'products.php')) ?>">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= e(url($base_path,'contact.php')) ?>">Contact</a></li>
      </ul>

      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item">
          <a class="nav-link" href="<?= e(url($base_path,'cart.php')) ?>">🛒 Cart <span class="badge bg-light text-dark"><?= (int)$cartCount ?></span></a>
        </li>

        <?php if ($user): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <?= e($user['name']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?= e(url($base_path,'profile.php')) ?>">Profile</a></li>
              <li><a class="dropdown-item" href="<?= e(url($base_path,'orders.php')) ?>">My Orders</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?= e(url($base_path,'logout.php')) ?>">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= e(url($base_path,'login.php')) ?>">Login</a></li>
          <li class="nav-item"><a class="btn btn-light btn-sm fw-bold" href="<?= e(url($base_path,'register.php')) ?>">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
