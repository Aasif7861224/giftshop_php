<?php
require __DIR__ . '/config/bootstrap.php';

$cart = $_SESSION['cart'] ?? [];
$total = cart_total();

include __DIR__ . '/includes/top.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Your Cart</h3>
  <a class="btn btn-outline-secondary btn-sm" href="<?= e(url($base_path,'products.php')) ?>">Continue Shopping</a>
</div>

<?php if (!$cart): ?>
  <div class="alert alert-info">Cart empty. Add some products 😊</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table align-middle bg-white">
      <thead>
        <tr>
          <th>Product</th>
          <th class="text-end">Price</th>
          <th style="width:170px;">Qty</th>
          <th class="text-end">Subtotal</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart as $item): ?>
          <?php $sub = ((float)$item['price']) * ((int)$item['qty']); ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <img src="<?= e($item['image'] ?: 'images/banner.jpg') ?>" style="width:56px;height:56px;object-fit:cover;border-radius:.5rem" alt="">
                <div>
                  <div class="fw-semibold"><?= e($item['name']) ?></div>
                  <div class="small text-muted">#<?= (int)$item['product_id'] ?></div>
                </div>
              </div>
            </td>
            <td class="text-end">₹<?= e(number_format((float)$item['price'],2)) ?></td>
            <td>
              <form method="post" action="<?= e(url($base_path,'cart_action.php')) ?>" class="d-flex gap-2">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                <input type="number" name="qty" min="1" value="<?= (int)$item['qty'] ?>" class="form-control form-control-sm">
                <button class="btn btn-sm btn-outline-secondary">Update</button>
              </form>
            </td>
            <td class="text-end fw-semibold">₹<?= e(number_format($sub,2)) ?></td>
            <td class="text-end">
              <form method="post" action="<?= e(url($base_path,'cart_action.php')) ?>">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                <button class="btn btn-sm btn-outline-danger">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <form method="post" action="<?= e(url($base_path,'cart_action.php')) ?>">
        <input type="hidden" name="action" value="clear">
        <button class="btn btn-outline-danger">Clear Cart</button>
      </form>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <span class="text-muted">Total</span>
            <span class="fw-bold">₹<?= e(number_format($total,2)) ?></span>
          </div>
          <a class="btn fw-bold w-100 mt-3" style="background:#c2185b;color:#fff" href="<?= e(url($base_path,'checkout.php')) ?>">
            Checkout
          </a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/bottom.php'; ?>
