<?php
require __DIR__ . '/config/bootstrap.php';

if (current_user()) {
    redirect(url($base_path,'index.php'));
}

$error = null;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $pass = (string)($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT id,name,email,password_hash FROM users WHERE email=:email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        flash_set('success','Welcome back, ' . $user['name'] . '!');
        redirect(url($base_path,'index.php'));
    } else {
        $error = 'Invalid email or password';
    }
}

include __DIR__ . '/includes/top.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card">
      <div class="card-body p-4">
        <h3 class="fw-bold mb-1">Login</h3>
        <div class="text-muted mb-3">Order history dekhne ke liye login required hai.</div>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input class="form-control" name="email" type="email" value="<?= e($email) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" name="password" type="password" required>
          </div>

          <button class="btn fw-bold w-100" style="background:#c2185b;color:#fff">Login</button>
        </form>

        <div class="text-center mt-3">
          <a href="<?= e(url($base_path,'register.php')) ?>">New user? Create account</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
