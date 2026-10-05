<?php
// ============================================================
//   MY DHIVA TAMIL STATIONERY HUB — Checkout
//   checkout.php
//   INSERT INTO orders + order_items, UPDATE product stock
// ============================================================
require_once __DIR__ . '/includes/db.php';
$current_page = 'checkout';

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Must be logged in to check out
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

// Load cart items fresh from the DB (never trust session prices)
$items    = [];
$subtotal = 0;
if ($db_ok) {
    $ids   = array_map('intval', array_keys($_SESSION['cart']));
    $in    = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $stmt  = mysqli_prepare($conn, "SELECT * FROM products WHERE id IN ($in)");
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

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name    = trim($_POST['customer_name'] ?? '');
    $address = trim($_POST['address']       ?? '');
    $phone   = trim($_POST['phone']         ?? '');

    if ($name === '' || $address === '' || $phone === '') {
        $errors[] = "Name, address, and phone are all required.";
    } elseif (empty($items)) {
        $errors[] = "Your cart is empty.";
    } else {
        try {
            mysqli_begin_transaction($conn);

            $stmt = mysqli_prepare($conn,
                "INSERT INTO orders (user_id, customer_name, address, phone, total_amount, status)
                 VALUES (?, ?, ?, ?, ?, 'Pending')"
            );
            mysqli_stmt_bind_param($stmt, "isssd", $_SESSION['user_id'], $name, $address, $phone, $grand);
            mysqli_stmt_execute($stmt);
            $order_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            foreach ($items as $it) {
                $p = $it['product'];

                $stmt2 = mysqli_prepare($conn,
                    "INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
                     VALUES (?, ?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param($stmt2, "iisdi", $order_id, $p['id'], $p['name'], $p['price'], $it['qty']);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);

                // Reduce stock, never below zero
                $stmt3 = mysqli_prepare($conn,
                    "UPDATE products SET quantity = GREATEST(quantity - ?, 0) WHERE id = ?"
                );
                mysqli_stmt_bind_param($stmt3, "ii", $it['qty'], $p['id']);
                mysqli_stmt_execute($stmt3);
                mysqli_stmt_close($stmt3);
            }

            mysqli_commit($conn);
            $_SESSION['cart'] = [];
            header("Location: order_success.php?id=$order_id");
            exit;

        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $errors[] = "Something went wrong while placing your order. Please try again.";
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top:24px">
  <h1 style="font-size:1.4rem;font-weight:900;color:var(--maroon);margin-bottom:20px">🧾 Checkout</h1>

  <?php if (!empty($errors)): ?>
    <div class="msg-banner error">
      <?php foreach ($errors as $e): ?><div><?= h($e) ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start">

    <!-- Delivery details form -->
    <div class="section">
      <h2>📍 Delivery Details</h2>
      <form method="POST">
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" name="customer_name" class="form-input"
                 value="<?= h($_POST['customer_name'] ?? $_SESSION['user_name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Delivery Address *</label>
          <textarea name="address" class="form-input" rows="3" required><?= h($_POST['address'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label>Phone Number *</label>
          <input type="text" name="phone" class="form-input" value="<?= h($_POST['phone'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Payment Method</label>
          <select class="form-input" disabled>
            <option>Cash on Delivery</option>
          </select>
          <div style="font-size:.75rem;color:var(--light);margin-top:4px">This demo only supports Cash on Delivery.</div>
        </div>
        <button type="submit" name="place_order" value="1" class="btn btn-primary" style="width:100%;justify-content:center">
          ✅ Place Order — ₹<?= number_format($grand, 2) ?>
        </button>
      </form>
    </div>

    <!-- Order summary -->
    <div class="section">
      <h2>🧾 Order Summary</h2>
      <?php foreach ($items as $it): $p = $it['product']; ?>
        <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:8px">
          <span><?= category_icon($p['category']) ?> <?= h($p['name']) ?> × <?= (int)$it['qty'] ?></span>
          <span style="font-weight:700">₹<?= number_format($it['line'], 2) ?></span>
        </div>
      <?php endforeach; ?>
      <div style="border-top:2px solid var(--cream2);margin-top:10px;padding-top:10px">
        <div class="row" style="display:flex;justify-content:space-between;font-size:.88rem;margin-bottom:6px">
          <span>Subtotal</span><span>₹<?= number_format($subtotal, 2) ?></span>
        </div>
        <div class="row" style="display:flex;justify-content:space-between;font-size:.88rem;margin-bottom:6px">
          <span>Shipping</span><span><?= $shipping === 0 ? 'FREE' : '₹' . number_format($shipping, 2) ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;font-weight:900;font-size:1.1rem;color:var(--maroon)">
          <span>Total</span><span>₹<?= number_format($grand, 2) ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
