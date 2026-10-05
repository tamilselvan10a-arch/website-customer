<?php
// ============================================================
//   logout.php — Destroys the session and returns to the shop
// ============================================================
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit;
