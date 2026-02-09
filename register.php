<?php
require __DIR__ . '/config/bootstrap.php';

if (current_user()) {
    redirect(url($base_path,'index.php'));
}

$errors = [];
$name = $email = $phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $pass = (string)($_POST['password'] ?? '');
    $pass2 = (string)($_POST['password2'] ?? '');

    if ($name === '') $errors[] = 'Name required';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
    if (strlen($pass) < 6) $errors[] = 'Password minimum 6 characters';
    if ($pass !== $pass2) $errors[] = 'Passwords do not match';

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email=:email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $ins = $pdo->prepare("INSERT INTO users (name,email,phone,password_hash) VALUES (:n,:e,:p,:h)");
            $ins->execute([':n'=>$name,':e'=>$email,':p'=>$phone?:null,':h'=>$hash]);
            flash_set('success','Account created. Please login.');
            redirect(url($base_path,'login.php'));
        }
    }
}

include __DIR__ . '/includes/top.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card">
      <div class="card-body p-4">
        <h3 class="fw-bold mb-1">Create Account</h3>
        <div class="text-muted mb-3">Login karne ke baad hi order history dikhegi.</div>

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-2">
            <label class="form-label">Full Name</label>
            <input class="form-control" name="name" value="<?= e($name) ?>" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Email</label>
            <input class="form-control" name="email" type="email" value="<?= e($email) ?>" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Phone (optional)</label>
            <input class="form-control" name="phone" value="<?= e($phone) ?>">
          </div>

          <div class="mb-2">
            <label class="form-label">Password</label>
            <input class="form-control" name="password" type="password" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input class="form-control" name="password2" type="password" required>
          </div>

          <button class="btn fw-bold w-100" style="background:#c2185b;color:#fff">Sign Up</button>
        </form>

        <div class="text-center mt-3">
          <a href="<?= e(url($base_path,'login.php')) ?>">Already have account? Login</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
