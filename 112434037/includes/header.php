<?php
// ============================================================
//   includes/header.php
//   Shared <head> + site header/nav for every customer page.
//   Expects (optionally) $page_title and $current_page to be
//   set by the including page before requiring this file.
// ============================================================
if (!isset($conn)) { require_once __DIR__ . '/db.php'; }
$current_page = $current_page ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? h($page_title) . ' — ' : '' ?>My Dhiva Tamil Stationery Hub</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Catamaran:wght@400;600;700;800;900&family=Kavivanar&display=swap" rel="stylesheet">
<style>
:root {
  --maroon:  #8B1A1A;
  --maroon2: #6B0F0F;
  --cream:   #FFF8F0;
  --cream2:  #F5EDE0;
  --gold:    #D4A017;
  --green:   #2D6A4F;
  --white:   #FFFFFF;
  --border:  #E8D5C4;
  --text:    #1A1A1A;
  --light:   #7A7A7A;
  --rad:     10px;
  --shadow:  0 2px 12px rgba(139,26,26,.10);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Catamaran',sans-serif;background:var(--cream);color:var(--text);font-size:15px}
a{color:var(--maroon);text-decoration:none}
a:hover{text-decoration:underline}
img{max-width:100%}
.container{max-width:1150px;margin:0 auto;padding:0 20px}

/* Header */
.site-header{background:var(--maroon);box-shadow:0 2px 8px rgba(0,0,0,.25)}
.header-top{background:var(--maroon2);padding:6px 0;font-size:.76rem;color:rgba(255,255,255,.7);text-align:center}
.header-main-row{display:flex;align-items:center;gap:18px;padding:14px 20px;max-width:1150px;margin:0 auto;flex-wrap:wrap}
.logo-box{display:flex;align-items:center;gap:10px;flex-shrink:0}
.logo-d{width:42px;height:42px;background:var(--gold);border-radius:8px;display:flex;align-items:center;justify-content:center;font-family:'Kavivanar',cursive;font-size:1.5rem;color:#fff;font-weight:700}
.logo-text .l1{font-size:1.05rem;font-weight:800;color:#fff;display:block}
.logo-text .l2{font-family:'Kavivanar',cursive;font-size:.6rem;color:var(--gold);display:block;letter-spacing:1px}
.search-form{flex:1;display:flex;max-width:420px;min-width:180px}
.search-form input{flex:1;padding:9px 14px;border:none;border-radius:8px 0 0 8px;font-family:'Catamaran',sans-serif;font-size:.85rem;outline:none}
.search-form button{background:var(--gold);border:none;color:#fff;padding:0 16px;border-radius:0 8px 8px 0;cursor:pointer;font-size:.9rem}
.site-nav{display:flex;gap:2px;align-items:center;margin-left:auto;flex-wrap:wrap}
.site-nav a{color:rgba(255,255,255,.88);padding:7px 12px;border-radius:6px;font-weight:600;font-size:.82rem;transition:background .2s;position:relative;white-space:nowrap}
.site-nav a:hover{background:rgba(255,255,255,.12);text-decoration:none;color:#fff}
.site-nav a.active{background:rgba(255,255,255,.16);color:var(--gold)}
.nav-user{color:rgba(255,255,255,.65);padding:7px 8px;font-size:.8rem;white-space:nowrap}
.cart-badge{background:var(--gold);color:#fff;font-size:.65rem;font-weight:800;padding:1px 6px;border-radius:10px;margin-left:4px}

/* Hero */
.hero{background:linear-gradient(135deg,var(--maroon2) 0%,var(--maroon) 60%,#9B2020 100%);padding:44px 20px;text-align:center;position:relative;overflow:hidden}
.hero::after{content:'ஓம்';position:absolute;right:-20px;top:50%;transform:translateY(-50%);font-family:'Kavivanar',cursive;font-size:10rem;color:rgba(212,160,23,.06);pointer-events:none}
.hero h1{font-size:2rem;font-weight:900;color:#fff;margin-bottom:8px}
.hero p{color:rgba(255,255,255,.78);font-size:.95rem;max-width:520px;margin:0 auto 20px}
.hero a.btn-shop{display:inline-block;background:var(--gold);color:#fff;padding:11px 28px;border-radius:24px;font-weight:800;font-size:.9rem}
.hero a.btn-shop:hover{background:#B8860B;text-decoration:none}

/* Alerts */
.msg-banner{padding:12px 20px;border-radius:var(--rad);font-weight:700;font-size:.88rem;margin:20px 0;border:1px solid}
.msg-banner.success{background:#D1FAE5;color:#065F46;border-color:#6EE7B7}
.msg-banner.error{background:#FEE2E2;color:#991B1B;border-color:#FCA5A5}

/* Shop layout */
.shop-layout{display:grid;grid-template-columns:220px 1fr;gap:24px;margin:28px 0}
.filters{background:var(--white);border:1px solid var(--border);border-radius:var(--rad);padding:18px;align-self:start;position:sticky;top:16px}
.filters h3{font-size:.85rem;color:var(--maroon);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px}
.filters a{display:block;padding:7px 10px;border-radius:6px;font-size:.85rem;color:var(--text);margin-bottom:2px}
.filters a:hover{background:var(--cream2);text-decoration:none}
.filters a.active{background:var(--maroon);color:#fff;font-weight:700}
.listing-head{font-size:.85rem;color:var(--light);margin-bottom:14px}

/* Product grid */
.product-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.prod-card{background:var(--white);border:1px solid var(--border);border-radius:var(--rad);overflow:hidden;box-shadow:var(--shadow);transition:transform .2s,box-shadow .2s;display:flex;flex-direction:column}
.prod-card:hover{transform:translateY(-3px)}
.prod-img{display:block;background:var(--cream2);text-align:center;padding:32px 0;font-size:3.4rem}
.prod-body{padding:14px 16px;display:flex;flex-direction:column;gap:5px;flex:1}
.prod-cat{font-size:.7rem;color:var(--light);text-transform:uppercase;letter-spacing:.4px;font-weight:700}
.prod-name{font-weight:800;color:var(--text);font-size:.95rem}
.prod-price{font-weight:900;color:var(--maroon);font-size:1.15rem;margin-top:auto}
.stock-out{color:#DC2626;font-weight:700;font-size:.78rem}
.btn-add{background:var(--maroon);color:#fff;border:none;padding:9px;border-radius:7px;font-family:'Catamaran',sans-serif;font-weight:700;font-size:.82rem;cursor:pointer;width:100%;margin-top:4px}
.btn-add:hover{background:var(--maroon2)}

/* Generic buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:11px 22px;border:none;border-radius:var(--rad);font-family:'Catamaran',sans-serif;font-weight:700;font-size:.88rem;cursor:pointer;transition:all .2s;text-decoration:none}
.btn:hover{text-decoration:none;transform:translateY(-1px)}
.btn-primary{background:var(--maroon);color:#fff}
.btn-primary:hover{background:var(--maroon2)}
.btn-outline{background:transparent;color:var(--maroon);border:2px solid var(--maroon)}
.btn-outline:hover{background:var(--maroon);color:#fff}
.btn-gold{background:var(--gold);color:#fff}
.btn-gold:hover{background:#B8860B}

/* Cart table */
.cart-table{width:100%;border-collapse:collapse;background:var(--white);border-radius:var(--rad);overflow:hidden;box-shadow:var(--shadow)}
.cart-table th{background:var(--maroon);color:#fff;padding:10px 14px;text-align:left;font-size:.78rem}
.cart-table td{padding:12px 14px;border-bottom:1px solid var(--border);vertical-align:middle;font-size:.88rem}
.cart-table input[type=number]{width:60px;padding:6px;border:1px solid var(--border);border-radius:6px}
.cart-summary{background:var(--white);border-radius:var(--rad);box-shadow:var(--shadow);padding:20px;margin-top:16px;max-width:340px;margin-left:auto}
.cart-summary .row{display:flex;justify-content:space-between;margin-bottom:8px;font-size:.9rem}
.cart-summary .total{font-weight:900;font-size:1.2rem;color:var(--maroon);border-top:2px solid var(--cream2);padding-top:10px;margin-top:6px}
.empty-box{background:var(--white);border-radius:var(--rad);padding:50px 20px;text-align:center;box-shadow:var(--shadow)}
.empty-box .icon{font-size:3.5rem;margin-bottom:14px}

/* Forms (auth + checkout) */
.section{background:var(--white);border:1px solid var(--border);border-radius:var(--rad);padding:28px;margin:24px 0;box-shadow:var(--shadow)}
.section h2{font-size:1.15rem;font-weight:800;color:var(--maroon);margin-bottom:18px}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:.78rem;font-weight:700;color:#555;margin-bottom:5px}
.form-input,.form-group textarea{width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:8px;font-family:'Catamaran',sans-serif;font-size:.9rem;color:var(--text);background:var(--cream);outline:none}
.form-input:focus,.form-group textarea:focus{border-color:var(--maroon);background:#fff}

/* Orders */
.order-card{background:var(--white);border:1px solid var(--border);border-radius:var(--rad);box-shadow:var(--shadow);margin-bottom:16px;overflow:hidden}
.order-head{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:var(--cream2);flex-wrap:wrap;gap:8px}
.order-status{padding:3px 12px;border-radius:20px;font-size:.72rem;font-weight:800}
.order-status.Pending{background:#FEF3C7;color:#92400E}
.order-status.Confirmed,.order-status.Delivered{background:#D1FAE5;color:#065F46}
.order-items-table{width:100%;border-collapse:collapse}
.order-items-table td{padding:8px 20px;font-size:.85rem;border-bottom:1px solid var(--border)}

/* Footer */
.site-footer{background:var(--maroon2);color:rgba(255,255,255,.7);text-align:center;padding:20px;font-size:.82rem;margin-top:40px}

@media(max-width:900px){
  .shop-layout{grid-template-columns:1fr}
  .filters{position:static}
  .product-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:600px){
  .product-grid{grid-template-columns:1fr}
  .search-form{order:3;max-width:none;width:100%}
}
</style>
</head>
<body>

<header class="site-header">
  <div class="header-top">
    📍 Chennai, Tamil Nadu &nbsp;|&nbsp; 📞 +91 98765 43210 &nbsp;|&nbsp; ✉️ hello@dhivastationery.in
  </div>
  <div class="header-main-row">
    <a href="index.php" class="logo-box">
      <div class="logo-d">D</div>
      <div class="logo-text">
        <span class="l1">Dhiva Stationery</span>
        <span class="l2">MY DHIVA TAMIL STATIONERY HUB</span>
      </div>
    </a>

    <form action="index.php" method="GET" class="search-form">
      <input type="text" name="search" placeholder="Search pens, notebooks, geometry sets..." value="<?= h($_GET['search'] ?? '') ?>">
      <button type="submit">🔍</button>
    </form>

    <nav class="site-nav">
      <a href="index.php" class="<?= $current_page === 'home' ? 'active' : '' ?>">🏠 Home</a>
      <a href="cart.php" class="<?= $current_page === 'cart' ? 'active' : '' ?>">
        🛒 Cart<?php $cc = cart_count(); if ($cc > 0): ?><span class="cart-badge"><?= $cc ?></span><?php endif; ?>
      </a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="orders.php" class="<?= $current_page === 'orders' ? 'active' : '' ?>">📦 My Orders</a>
        <span class="nav-user">👤 <?= h(explode(' ', $_SESSION['user_name'])[0]) ?></span>
        <a href="logout.php">🚪 Logout</a>
      <?php else: ?>
        <a href="login.php" class="<?= $current_page === 'login' ? 'active' : '' ?>">🔑 Login</a>
        <a href="register.php" class="<?= $current_page === 'register' ? 'active' : '' ?>">✍️ Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
