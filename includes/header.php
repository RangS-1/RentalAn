<?php
if (!isset($_SESSION)) session_start();
$base = baseUrl();
$role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>RentalKu Elektronik</title>
<link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <div class="nav-container">
    <a href="<?= $base ?><?= $role === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php' ?>" class="logo">⚡ RentalAn</a>
    <?php if (isset($_SESSION['user_id'])): ?>
      <div class="nav-links">
        <?php if ($role === 'admin'): ?>
          <a href="<?= $base ?>admin/dashboard.php">Dashboard</a>
          <a href="<?= $base ?>admin/items.php">Kelola Barang</a>
          <a href="<?= $base ?>admin/users.php">Pengguna</a>
          <a href="<?= $base ?>admin/confirm_borrow.php">Konfirmasi Pinjam</a>
          <a href="<?= $base ?>admin/confirm_return.php">Konfirmasi Kembali</a>
        <?php else: ?>
          <a href="<?= $base ?>user/dashboard.php">Katalog</a>
          <a href="<?= $base ?>user/my_rentals.php">Peminjaman Saya</a>
          <a href="<?= $base ?>user/account.php">Akun</a>
        <?php endif; ?>
        <span class="user-badge">👤 <?= e($_SESSION['full_name']) ?> (<?= e($role) ?>)</span>
        <a href="<?= $base ?>logout.php" class="btn-logout">Keluar</a>
      </div>
    <?php endif; ?>
  </div>
</nav>
<main class="container">
