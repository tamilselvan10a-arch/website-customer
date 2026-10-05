<?php
// ============================================================
//   MY DHIVA TAMIL STATIONERY HUB — Customer Registration
//   register.php
// ============================================================
require_once __DIR__ . '/includes/db.php';
$current_page = 'register';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Ensure users table exists (in case this site runs standalone)
if ($db_ok) {
    mysqli_query($conn,
        "CREATE TABLE IF NOT EXISTS users (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            name       VARCHAR(100) NOT NULL,
            email      VARCHAR(150) NOT NULL,
            password   VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
}

$errors  = [];
$success = false;
$old     = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']            ?? '');
    $email    = trim($_POST['email']           ?? '');
    $password = $_POST['password']             ?? '';
    $confirm  = $_POST['confirm_password']     ?? '';
    $old      = ['name' => $name, 'email' => $email];

    if (strlen($name) < 2)                     $errors[] = "Name must be at least 2 characters.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email address.";
     if (strlen($password) < 8)
        $errors[] = "Password must be at least 8 characters.";

    if (!preg_match('/[A-Z]/', $password))
        $errors[] = "Password must contain at least one uppercase letter.";

    if (!preg_match('/[a-z]/', $password))
        $errors[] = "Password must contain at least one lowercase letter.";

    if (!preg_match('/[0-9]/', $password))
        $errors[] = "Password must contain at least one number.";

    if (!preg_match('/[!@#$%^&*]/', $password))
        $errors[] = "Password must contain at least one special character (! @ # $ % ^ & *).";

    if (preg_match('/\s/', $password))
        $errors[] = "Spaces are not allowed in the password.";

    if ($password !== $confirm)
        $errors[] = "Passwords do not match.";
               

    if (empty($errors) && $db_ok) {
        $chk = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($chk, "s", $email);
        mysqli_stmt_execute($chk);
        mysqli_stmt_store_result($chk);
        if (mysqli_stmt_num_rows($chk) > 0) {
            $errors[] = "This email is already registered. Please log in.";
        }
        mysqli_stmt_close($chk);
    }

    if (empty($errors) && $db_ok) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success = true;
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:480px;padding-top:40px;padding-bottom:40px">
  <div class="section">
    <?php if ($success): ?>
      <div style="text-align:center;padding:10px 0">
        <div style="font-size:3rem;margin-bottom:10px">🎉</div>
        <h2 style="color:var(--green);margin-bottom:6px">Account Created!</h2>
        <p style="color:var(--light);margin-bottom:20px">You can now log in with your email and password.</p>
        <a href="login.php" class="btn btn-primary">Go to Login →</a>
      </div>
    <?php else: ?>
      <h2 style="text-align:center">✍️ Create Your Account</h2>

      <?php if (!empty($errors)): ?>
        <div class="msg-banner error">
          <ul style="margin-left:16px">
            <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" name="name" class="form-input" value="<?= h($old['name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Email Address *</label>
          <input type="email" name="email" class="form-input" value="<?= h($old['email']) ?>" required>
        </div>
        <div class="form-group">
          <label>Password * (min 6 chars)</label>
          <input type="password" name="password" class="form-input" minlength="6" required>
        </div>
        <div class="form-group">
          <label>Confirm Password *</label>
          <input type="password" name="confirm_password" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">✍️ Create Account</button>
      </form>

      <p style="text-align:center;margin-top:16px;font-size:.85rem;color:var(--light)">
        Already registered? <a href="login.php">Login here</a>
      </p>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
