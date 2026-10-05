<?php
// ============================================================
//   MY DHIVA TAMIL STATIONERY HUB — Customer Cart
//   cart.php
// ============================================================
require_once __DIR__ . '/includes/db.php';
$current_page = 'cart';

// Handle quantity updates / item removal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart']) && !empty($_POST['qty'])) {
        foreach ($_POST['qty'] as $pid => $q) {
            $pid = (int)$pid;
            $q   = (int)$q;
            if ($q <= 0) {
                unset($_SESSION['cart'][$pid]);
            } else {
                $_SESSION['cart'][$pid] = $q;
            }
        }
    } elseif (isset($_POST['remove_id'])) {
        unset($_SESSION['cart'][(int)$_POST['remove_id']]);
    }
    header("Location: cart.php");
    exit;
}

$items    = [];
$subtotal = 0;

if ($db_ok && !empty($_SESSION['cart'])) {
    $ids    = array_map('intval', array_keys($_SESSION['cart']));
    $in     = implode(',', array_fill(0, count($ids), '?'));
    $types  = str_repeat('i', count($ids));
    $stmt   = mysqli_prepare($conn, "SELECT * FROM products WHERE id IN ($in)");
    mysqli_stmt_bind_param($stmt, $types, ...$ids);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $qty  = $_SESSION['cart'][$row['id']];
        $line = $qty * (float)$row['price'];
        $subtotal += $line;
        $items[] = ['product' => $row, 'qty' => $qty, 'line' => $line];
    }
    mysqli_stmt_close($stmt);
}

$shipping = $subtotal > 0 ? ($subtotal >= 500 ? 0 : 40) : 0;
$grand    = $subtotal + $shipping;

require __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top:24px">
  <h1 style="font-size:1.4rem;font-weight:900;color:var(--maroon);margin-bottom:20px">🛒 Your Cart</h1>

  <?php if (empty($items)): ?>
    <div class="empty-box">
      <div class="icon">🛒</div>
      <h2 style="margin-bottom:8px;color:var(--maroon)">Your cart is empty</h2>
      <p style="color:var(--light);margin-bottom:20px">Browse our products and add something you like!</p>
      <a href="index.php" class="btn btn-primary">Start Shopping</a>
    </div>

  <?php else: ?>
    <form method="POST">
      <input type="hidden" name="update_cart" value="1">
      <table class="cart-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $it): $p = $it['product']; ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <span style="font-size:1.6rem"><?= category_icon($p['category']) ?></span>
                  <a href="product.php?id=<?= (int)$p['id'] ?>" style="font-weight:700;color:var(--text)"><?= h($p['name']) ?></a>
                </div>
              </td>
              <td>₹<?= number_format((float)$p['price'], 2) ?></td>
              <td>
                <input type="number" name="qty[<?= (int)$p['id'] ?>]" value="<?= (int)$it['qty'] ?>" min="0" max="<?= (int)$p['quantity'] ?>">
              </td>
              <td style="font-weight:800;color:var(--maroon)">₹<?= number_format($it['line'], 2) ?></td>
              <td>
                <button type="submit" form="remove-<?= (int)$p['id'] ?>" style="background:#FEE2E2;color:#DC2626;border:1px solid #FCA5A5;padding:6px 10px;border-radius:6px;font-weight:700;cursor:pointer">✕</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="margin-top:14px">
        <button type="submit" class="btn btn-outline">🔄 Update Cart</button>
        <a href="index.php" class="btn" style="background:var(--cream2);color:var(--maroon)">← Continue Shopping</a>
      </div>
    </form>

    <!-- Separate tiny forms for the ✕ remove buttons -->
    <?php foreach ($items as $it): $p = $it['product']; ?>
      <form id="remove-<?= (int)$p['id'] ?>" method="POST" style="display:none">
        <input type="hidden" name="remove_id" value="<?= (int)$p['id'] ?>">
      </form>
    <?php endforeach; ?>

    <div class="cart-summary">
      <div class="row"><span>Subtotal</span><span>₹<?= number_format($subtotal, 2) ?></span></div>
      <div class="row"><span>Shipping</span><span><?= $shipping === 0 ? 'FREE' : '₹' . number_format($shipping, 2) ?></span></div>
      <?php if ($shipping > 0): ?>
        <div style="font-size:.75rem;color:var(--light);margin-bottom:8px">Free shipping on orders over ₹500</div>
      <?php endif; ?>
      <div class="row total"><span>Total</span><span>₹<?= number_format($grand, 2) ?></span></div>
      <a href="checkout.php" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:14px">Proceed to Checkout →</a>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
