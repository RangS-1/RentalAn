<?php
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$itemCount = $db->query("SELECT COUNT(*) FROM items")->fetchColumn();
$userCount = $db->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$pendingBorrow = $db->query("SELECT COUNT(*) FROM rentals WHERE status='pending_borrow'")->fetchColumn();
$pendingReturn = $db->query("SELECT COUNT(*) FROM rentals WHERE status='pending_return'")->fetchColumn();
$active = $db->query("SELECT COUNT(*) FROM rentals WHERE status='active'")->fetchColumn();

$pageTitle = 'Dashboard Admin';
include __DIR__ . '/../includes/header.php';
?>
<h1>🛠️ Dashboard Admin</h1>
<p style="color:#6b7280;margin-bottom:20px">Selamat datang, <b><?= e($_SESSION['full_name']) ?></b>!</p>

<div class="stats">
  <div class="stat-card"><div class="stat-label">Total Barang</div><div class="stat-value"><?= $itemCount ?></div></div>
  <div class="stat-card ok"><div class="stat-label">Pengguna Terdaftar</div><div class="stat-value"><?= $userCount ?></div></div>
  <div class="stat-card warn"><div class="stat-label">Menunggu Konfirmasi Pinjam</div><div class="stat-value"><?= $pendingBorrow ?></div></div>
  <div class="stat-card warn"><div class="stat-label">Menunggu Konfirmasi Kembali</div><div class="stat-value"><?= $pendingReturn ?></div></div>
  <div class="stat-card"><div class="stat-label">Sedang Dipinjam</div><div class="stat-value"><?= $active ?></div></div>
</div>

<div class="card">
  <h2>Aksi Cepat</h2>
  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px">
    <a href="items.php?action=add" class="btn btn-primary">+ Tambah Barang</a>
    <a href="items.php" class="btn btn-secondary">Kelola Barang</a>
    <a href="users.php" class="btn btn-secondary">Lihat Pengguna</a>
    <a href="confirm_borrow.php" class="btn btn-warning">Konfirmasi Pinjam (<?= $pendingBorrow ?>)</a>
    <a href="confirm_return.php" class="btn btn-warning">Konfirmasi Kembali (<?= $pendingReturn ?>)</a>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
