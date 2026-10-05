<?php
// ============================================================
//   MY DHIVA TAMIL STATIONERY HUB — Customer Product Detail
//   product.php?id=1
// ============================================================
require_once __DIR__ . '/includes/db.php';
$current_page = 'product';

$id      = (int)($_GET['id'] ?? 0);
$product = null;

if ($db_ok && $id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

$related = [];
if ($db_ok && $product) {
    $stmt = mysqli_prepare($conn,
        "SELECT * FROM products WHERE category = ? AND id != ? ORDER BY id DESC LIMIT 4"
    );
    mysqli_stmt_bind_param($stmt, "si", $product['category'], $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $related[] = $row;
    }
    mysqli_stmt_close($stmt);
}

$page_title = $product ? $product['name'] : 'Product Not Found';
require __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top:24px">

  <?php if (!$db_ok): ?>
    <div class="msg-banner error">✗ Unable to load this product right now.</div>

  <?php elseif (!$product): ?>
    <div class="empty-box">
      <div class="icon">📦</div>
      <h2 style="margin-bottom:8px;color:var(--maroon)">Product Not Found</h2>
      <p style="color:var(--light);margin-bottom:20px">No product with ID <?= $id ?> exists.</p>
      <a href="index.php" class="btn btn-primary">← Back to Shop</a>
    </div>

  <?php else: ?>
    <?php
    $qty = (int)$product['quantity'];
    if ($qty === 0)      { $stockClass = '#DC2626'; $stockText = 'Out of Stock'; }
    elseif ($qty < 10)   { $stockClass = '#B45309'; $stockText = "Only $qty left — order soon"; }
    else                 { $stockClass = '#2D6A4F'; $stockText = "In Stock ($qty available)"; }
    ?>
    <div style="font-size:.82rem;color:var(--light);margin-bottom:16px">
      <a href="index.php">Home</a> ›
      <a href="index.php?category=<?= urlencode($product['category']) ?>"><?= h($product['category']) ?></a> ›
      <?= h($product['name']) ?>
    </div>

    <div class="section" style="display:grid;grid-template-columns:320px 1fr;gap:32px;padding:0;overflow:hidden">
      <div style="background:var(--cream2);display:flex;align-items:center;justify-content:center;font-size:6.5rem;min-height:280px">
        <?= category_icon($product['category']) ?>
      </div>
      <div style="padding:28px 28px 28px 0">
        <div style="font-size:.75rem;color:var(--light);font-weight:700;margin-bottom:6px">Product ID: #<?= (int)$product['id'] ?></div>
        <h1 style="font-size:1.6rem;font-weight:900;margin-bottom:8px"><?= h($product['name']) ?></h1>
        <span style="display:inline-block;background:var(--cream2);color:var(--maroon);padding:4px 12px;border-radius:20px;font-size:.78rem;font-weight:700;margin-bottom:14px">
          <?= h($product['category']) ?>
        </span>
        <div style="font-size:2rem;font-weight:900;color:var(--maroon);margin-bottom:10px">
          ₹<?= number_format((float)$product['price'], 2) ?>
          <span style="font-size:.9rem;font-weight:600;color:var(--light)">per unit</span>
        </div>
        <div style="font-weight:700;color:<?= $stockClass ?>;margin-bottom:18px">● <?= $stockText ?></div>

        <?php if (!empty($product['description'])): ?>
          <p style="color:var(--light);font-size:.9rem;line-height:1.7;margin-bottom:20px"><?= nl2br(h($product['description'])) ?></p>
        <?php endif; ?>

        <?php if ($qty > 0): ?>
          <form method="POST" action="add_to_cart.php" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <input type="hidden" name="redirect" value="product.php?id=<?= (int)$product['id'] ?>">
            <label style="font-size:.82rem;font-weight:700">Qty:</label>
            <input type="number" name="qty" value="1" min="1" max="<?= $qty ?>"
                   style="width:70px;padding:8px;border:1.5px solid var(--border);border-radius:8px">
            <button type="submit" class="btn btn-primary">🛒 Add to Cart</button>
            <a href="cart.php" class="btn btn-outline">View Cart</a>
          </form>
        <?php else: ?>
          <div class="msg-banner error" style="margin:0">This product is currently out of stock.</div>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($related)): ?>
      <h2 style="font-size:1.1rem;font-weight:800;color:var(--maroon);margin:28px 0 14px">
        📂 More in <?= h($product['category']) ?>
      </h2>
      <div class="product-grid">
        <?php foreach ($related as $r): ?>
          <div class="prod-card">
            <a href="product.php?id=<?= (int)$r['id'] ?>" class="prod-img"><?= category_icon($r['category']) ?></a>
            <div class="prod-body">
              <div class="prod-cat"><?= h($r['category']) ?></div>
              <a href="product.php?id=<?= (int)$r['id'] ?>" class="prod-name"><?= h($r['name']) ?></a>
              <div class="prod-price">₹<?= number_format((float)$r['price'], 2) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php endif; ?>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
