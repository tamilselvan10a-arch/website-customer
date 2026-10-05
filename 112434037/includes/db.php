<?php
// ============================================================
//   MY DHIVA TAMIL STATIONERY HUB — Customer Site
//   includes/db.php
//   Shared DB connection + helpers, included by every page
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connects to the SAME database used by the seller-side admin site
$conn  = mysqli_connect("localhost", "root", "", "dhiva_stationery");
$db_ok = false;

if ($conn) {
    $db_ok = true;
    mysqli_set_charset($conn, "utf8mb4");

    // Auto-create the customer-facing tables if they don't exist yet
    mysqli_query($conn,
        "CREATE TABLE IF NOT EXISTS orders (
            id            INT AUTO_INCREMENT PRIMARY KEY,
            user_id       INT NOT NULL,
            customer_name VARCHAR(150) NOT NULL,
            address       TEXT NOT NULL,
            phone         VARCHAR(20) NOT NULL,
            total_amount  DECIMAL(10,2) NOT NULL,
            status        VARCHAR(30) NOT NULL DEFAULT 'Pending',
            created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
    mysqli_query($conn,
        "CREATE TABLE IF NOT EXISTS order_items (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            order_id     INT NOT NULL,
            product_id   INT NOT NULL,
            product_name VARCHAR(150) NOT NULL,
            price        DECIMAL(10,2) NOT NULL,
            quantity     INT NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
}

// Safe-echo helper
function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Total number of items currently in the cart (for the header badge)
function cart_count() {
    if (empty($_SESSION['cart'])) return 0;
    return array_sum($_SESSION['cart']);
}

// Category → emoji icon map, shared across all customer pages
function category_icon($cat) {
    $icons = [
        'Pens'            => '🖊️',
        'Pencils'         => '✏️',
        'Notebooks'       => '📒',
        'Geometry'        => '📐',
        'Art & Craft'     => '🎨',
        'School Supplies' => '🎒',
        'Office Supplies' => '📁',
        'Markers'         => '🖍️',
        'Accessories'     => '📎',
    ];
    return $icons[$cat] ?? '📦';
}

// All categories offered on the seller side, reused here for the filter sidebar
function all_categories() {
    return ['Pens','Pencils','Notebooks','Geometry','Art & Craft',
            'School Supplies','Office Supplies','Markers','Accessories'];
}
