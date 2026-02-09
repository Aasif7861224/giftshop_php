<?php
require __DIR__ . '/../config/bootstrap.php';

if (admin_user()) {
    redirect(url($base_path,'admin/index.php'));
}

$error = null;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $pass = (string)($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT id,name,email,password_hash,role FROM admins WHERE email=:email LIMIT 1");
    $stmt->execute([':email'=>$email]);
    $adm = $stmt->fetch();

    if ($adm && password_verify($pass, $adm['password_hash'])) {
        unset($adm['password_hash']);
        $_SESSION['admin'] = $adm;
        redirect(url($base_path,'admin/index.php'));
    } else {
        $error = 'Invalid admin credentials';
    }
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card">
        <div class="card-body p-4">
          <h3 class="fw-bold mb-1">Admin Login</h3>
          <div class="text-muted mb-3">admin@demo.com / Admin@123</div>
          <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
          <form method="post">
            <div class="mb-2">
              <label class="form-label">Email</label>
              <input class="form-control" name="email" type="email" value="<?= e($email) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input class="form-control" name="password" type="password" required>
            </div>
            <button class="btn btn-dark fw-bold w-100">Login</button>
          </form>
          <div class="text-center mt-3">
            <a href="<?= e(url($base_path,'index.php')) ?>">Back to site</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
