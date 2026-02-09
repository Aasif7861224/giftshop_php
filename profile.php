<?php
require __DIR__ . '/config/bootstrap.php';
require_login($base_path);

$user = current_user();

$stmt = $pdo->prepare("SELECT id,name,email,phone,created_at FROM users WHERE id=:id LIMIT 1");
$stmt->execute([':id' => $user['id']]);
$u = $stmt->fetch();

include __DIR__ . '/includes/top.php';
?>
<h3 class="mb-3">My Profile</h3>

<div class="card">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="text-muted small">Name</div>
        <div class="fw-semibold"><?= e($u['name']) ?></div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Email</div>
        <div class="fw-semibold"><?= e($u['email']) ?></div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Phone</div>
        <div class="fw-semibold"><?= e((string)($u['phone'] ?? '-')) ?></div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Joined</div>
        <div class="fw-semibold"><?= e($u['created_at']) ?></div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/bottom.php'; ?>
