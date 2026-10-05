<?php
// ============================================================
//   MY DHIVA TAMIL STATIONERY HUB — Customer Login
//   login.php
// ============================================================
require_once __DIR__ . '/includes/db.php';
$current_page = 'login';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error    = '';
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $oldEmail = $email;

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } elseif (!$db_ok) {
        $error = "Database connection failed. Please try again.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$user || !password_verify($password, $user['password'])) {
            $error = "Incorrect email or password.";
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: " . $redirect);
            exit;
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:440px;padding-top:40px;padding-bottom:40px">
  <div class="section">
    <h2 style="text-align:center">🔑 Login to Your Account</h2>

    <?php if ($error): ?>
      <div class="msg-banner error"><?= h($error) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['registered'])): ?>
      <div class="msg-banner success">✓ Account created! Please log in below.</div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="form-group">
        <label>Email Address *</label>
        <input type="email" name="email" class="form-input" value="<?= h($oldEmail) ?>" required>
      </div>
      <div class="form-group">
        <label>Password *</label>
        <input type="password" name="password" class="form-input" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">🔑 Login</button>
    </form>

    <p style="text-align:center;margin-top:16px;font-size:.85rem;color:var(--light)">
      Don't have an account? <a href="register.php">Register here</a>
    </p>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
