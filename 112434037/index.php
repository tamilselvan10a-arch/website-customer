<?php
// ============================================================
//   MY DHIVA TAMIL STATIONERY HUB — Customer Home / Catalog
//   index.php
// ============================================================
require_once __DIR__ . '/includes/db.php';
$current_page = 'home';

$search   = trim($_GET['search']   ?? '');
$category = trim($_GET['category'] ?? '');

$products = [];
if ($db_ok) {
    $sql    = "SELECT * FROM products WHERE 1=1";
    $types  = "";
    $params = [];

    if ($search !== '') {
        $sql     .= " AND name LIKE ?";
        $types   .= "s";
        $params[] = "%$search%";
    }
    if ($category !== '') {
        $sql     .= " AND category = ?";
        $types   .= "s";
        $params[] = $category;
    }
    $sql .= " ORDER BY id DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== "") {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $products[] = $row;
    }
    mysqli_stmt_close($stmt);
}

$msg = $_GET['msg'] ?? '';

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <h1>✒️ Everything for Your Desk, Delivered</h1>
  <p>Pens, notebooks, art supplies and more from Chennai's trusted stationery hub.</p>
  <a href="#shop" class="btn-shop">Shop Now ↓</a>
</section>

<div class="container">

  <?php if (!$db_ok): ?>
    <div class="msg-banner error">✗ Unable to load products right now. Please try again shortly.</div>
  <?php endif; ?>

  <?php if ($msg): ?>
    <div class="msg-banner success"><?= h($msg) ?></div>
  <?php endif; ?>

  <div class="shop-layout" id="shop">

    <!-- Category sidebar -->
    <aside class="filters">
      <h3>Categories</h3>
      <a href="index.php" class="<?= $category === '' ? 'active' : '' ?>">🗂️ All Products</a>
      <?php foreach (all_categories() as $c): ?>
        <a href="index.php?category=<?= urlencode($c) ?>" class="<?= $category === $c ? 'active' : '' ?>">
          <?= category_icon($c) ?> <?= h($c) ?>
        </a>
      <?php endforeach; ?>
    </aside>

    <!-- Product grid -->
    <div class="product-listing">
      <div class="listing-head">
        <?= count($products) ?> product(s) found
        <?php if ($search !== ''): ?> for "<strong><?= h($search) ?></strong>"<?php endif; ?>
        <?php if ($category !== ''): ?> in <strong><?= h($category) ?></strong>
          &nbsp;<a href="index.php">✕ clear</a>
        <?php endif; ?>
      </div>

      <div class="product-grid">
        <?php if (empty($products)): ?>
          <p style="color:var(--light)">No products match your search. Try a different keyword or category.</p>
        <?php else: ?>
          <?php foreach ($products as $p): ?>
            <div class="prod-card">
              <a href="product.php?id=<?= (int)$p['id'] ?>" class="prod-img"><?= category_icon($p['category']) ?></a>
              <div class="prod-body">
                <div class="prod-cat"><?= h($p['category']) ?></div>
                <a href="product.php?id=<?= (int)$p['id'] ?>" class="prod-name"><?= h($p['name']) ?></a>
                <div class="prod-price">₹<?= number_format((float)$p['price'], 2) ?></div>

                <?php if ((int)$p['quantity'] === 0): ?>
                  <div class="stock-out">Out of Stock</div>
                <?php else: ?>
                  <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                    <input type="hidden" name="qty" value="1">
                    <input type="hidden" name="redirect" value="index.php<?= $category !== '' ? '?category=' . urlencode($category) : '' ?>">
                    <button type="submit" class="btn-add">🛒 Add to Cart</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

  </div><!-- /shop-layout -->
</div><!-- /container -->

<?php require __DIR__ . '/includes/footer.php'; ?>
