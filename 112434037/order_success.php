<?php
// ============================================================
//   order_success.php?id=1
//   Confirmation page shown immediately after placing an order
// ============================================================
require_once __DIR__ . '/includes/db.php';
$current_page = '';

$id     = (int)($_GET['id'] ?? 0);
$order  = null;
$items  = [];

if ($db_ok && $id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($order) {
        $stmt2 = mysqli_prepare($conn, "SELECT * FROM order_items WHERE order_id = ?");
        mysqli_stmt_bind_param($stmt2, "i", $id);
        mysqli_stmt_execute($stmt2);
        $res = mysqli_stmt_get_result($stmt2);
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = $row;
        }
        mysqli_stmt_close($stmt2);
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top:24px">
  <?php if (!$order): ?>
    <div class="empty-box">
      <div class="icon">❓</div>
      <h2 style="color:var(--maroon);margin-bottom:8px">Order Not Found</h2>
      <a href="index.php" class="btn btn-primary">← Back to Shop</a>
    </div>
  <?php else: ?>
    <div class="empty-box" style="text-align:left;padding:36px">
      <div style="text-align:center;margin-bottom:20px">
        <div class="icon">🎉</div>
        <h2 style="color:var(--green);font-size:1.3rem;margin-bottom:6px">Order Placed Successfully!</h2>
        <p style="color:var(--light)">Order <strong>#<?= (int)$order['id'] ?></strong> has been received and is <strong><?= h($order['status']) ?></strong>.</p>
      </div>

      <table class="order-items-table" style="margin-bottom:16px">
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= h($it['product_name']) ?> × <?= (int)$it['quantity'] ?></td>
            <td style="text-align:right;font-weight:700">₹<?= number_format($it['price'] * $it['quantity'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
      <div style="text-align:right;font-weight:900;font-size:1.2rem;color:var(--maroon);margin-bottom:20px">
        Total: ₹<?= number_format($order['total_amount'], 2) ?>
      </div>

      <div style="font-size:.85rem;color:var(--light);margin-bottom:20px">
        📍 Delivering to: <?= h($order['address']) ?> &nbsp;|&nbsp; 📞 <?= h($order['phone']) ?>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center">
        <a href="orders.php" class="btn btn-primary">📦 View My Orders</a>
        <a href="index.php" class="btn btn-outline">Continue Shopping</a>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
