<?php
// ============================================================
//   MY DHIVA TAMIL STATIONERY HUB — My Orders
//   orders.php
// ============================================================
require_once __DIR__ . '/includes/db.php';
$current_page = 'orders';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'orders.php';
    header("Location: login.php");
    exit;
}

$orders = [];
if ($db_ok) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        // fetch line items for each order
        $stmt2 = mysqli_prepare($conn, "SELECT * FROM order_items WHERE order_id = ?");
        mysqli_stmt_bind_param($stmt2, "i", $row['id']);
        mysqli_stmt_execute($stmt2);
        $res2 = mysqli_stmt_get_result($stmt2);
        $row['items'] = [];
        while ($it = mysqli_fetch_assoc($res2)) {
            $row['items'][] = $it;
        }
        mysqli_stmt_close($stmt2);
        $orders[] = $row;
    }
    mysqli_stmt_close($stmt);
}

require __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top:24px">
  <h1 style="font-size:1.4rem;font-weight:900;color:var(--maroon);margin-bottom:20px">📦 My Orders</h1>

  <?php if (empty($orders)): ?>
    <div class="empty-box">
      <div class="icon">📭</div>
      <h2 style="color:var(--maroon);margin-bottom:8px">No orders yet</h2>
      <p style="color:var(--light);margin-bottom:20px">Once you place an order, it will show up here.</p>
      <a href="index.php" class="btn btn-primary">Start Shopping</a>
    </div>
  <?php else: ?>
    <?php foreach ($orders as $o): ?>
      <div class="order-card">
        <div class="order-head">
          <div>
            <strong>Order #<?= (int)$o['id'] ?></strong>
            &nbsp;<span style="color:var(--light);font-size:.82rem"><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></span>
          </div>
          <div style="display:flex;align-items:center;gap:12px">
            <span class="order-status <?= h($o['status']) ?>"><?= h($o['status']) ?></span>
            <strong style="color:var(--maroon)">₹<?= number_format($o['total_amount'], 2) ?></strong>
          </div>
        </div>
        <table class="order-items-table">
          <?php foreach ($o['items'] as $it): ?>
            <tr>
              <td><?= h($it['product_name']) ?> × <?= (int)$it['quantity'] ?></td>
              <td style="text-align:right;font-weight:700">₹<?= number_format($it['price'] * $it['quantity'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
        <div style="padding:10px 20px;font-size:.8rem;color:var(--light)">
          📍 <?= h($o['address']) ?> &nbsp;|&nbsp; 📞 <?= h($o['phone']) ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
