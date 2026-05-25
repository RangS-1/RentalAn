<?php
require_once __DIR__ . '/includes/config.php';
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (strlen($username) < 3 || strlen($password) < 6 || empty($full_name)) {
        $error = 'Username min 3 karakter, password min 6 karakter, nama lengkap wajib diisi.';
    } else {
        $u = new User($db);
        if ($u->usernameExists($username)) {
            $error = 'Username sudah digunakan.';
        } else {
            if ($u->register($username, $password, $full_name, $email, $phone)) {
                $success = 'Pendaftaran berhasil! Silakan masuk.';
            } else {
                $error = 'Gagal mendaftar. Coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Daftar — RentalKu</title><link rel="stylesheet" href="assets/css/style.css">
</head><body>
<div class="auth-wrapper"><div class="auth-card">
  <h1>⚡ RentalKu</h1>
  <h2 style="text-align:center;margin-bottom:20px;color:#6b7280;font-weight:500">Buat akun baru</h2>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?> <a href="login.php">Masuk &rarr;</a></div><?php endif; ?>
  <form method="post">
    <div class="form-group"><label>Username *</label><input type="text" name="username" class="form-control" required></div>
    <div class="form-group"><label>Nama Lengkap *</label><input type="text" name="full_name" class="form-control" required></div>
    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control"></div>
    <div class="form-group"><label>No. Telepon</label><input type="text" name="phone" class="form-control"></div>
    <div class="form-group"><label>Password * (min 6 karakter)</label><input type="password" name="password" class="form-control" required minlength="6"></div>
    <button type="submit" class="btn btn-primary btn-block">Daftar</button>
  </form>
  <p>Sudah punya akun? <a href="login.php">Masuk</a></p>
</div></div></body></html>
