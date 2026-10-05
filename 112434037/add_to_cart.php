<?php
// ============================================================
//   add_to_cart.php
//   Adds a product to the session cart, then redirects back.
//   Cart is stored as $_SESSION['cart'][product_id] = quantity
// ============================================================
require_once __DIR__ . '/includes/db.php';

$product_id = (int)($_POST['product_id'] ?? 0);
$qty        = max(1, (int)($_POST['qty'] ?? 1));
$redirect   = $_POST['redirect'] ?? 'index.php';

if ($product_id > 0 && $db_ok) {
    // Confirm the product actually exists before adding it
    $stmt = mysqli_prepare($conn, "SELECT id FROM products WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $exists = mysqli_stmt_get_result($stmt)->num_rows > 0;
    mysqli_stmt_close($stmt);

    if ($exists) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + $qty;
    }
}

$sep = (strpos($redirect, '?') !== false) ? '&' : '?';
header("Location: " . $redirect . $sep . "msg=" . urlencode("Added to your cart!"));
exit;
