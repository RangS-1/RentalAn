<?php
require_once __DIR__ . '/includes/config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = (new User($db))->login($username, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
        exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Masuk — RentalAn</title><link rel="stylesheet" href="assets/css/style.css">
</head><body>
<div class="auth-wrapper"><div class="auth-card">
  <h1>⚡ RentalAn</h1>
  <h2 style="text-align:center;margin-bottom:20px;color:#6b7280;font-weight:500">Masuk ke akun Anda</h2>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="form-group"><label>Username</label><input type="text" name="username" class="form-control" required autofocus></div>
    <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" required></div>
    <button type="submit" class="btn btn-primary btn-block">Masuk</button>
  </form>
  <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</div></div></body></html>
