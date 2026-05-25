<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$user = (new User($db))->getById($_SESSION['user_id']);
$pageTitle = 'Akun Saya';
include __DIR__ . '/../includes/header.php';
?>
<h1>👤 Informasi Akun</h1>
<div class="card">
  <div class="info-row"><span class="info-label">Username</span><span class="info-value"><?= e($user['username']) ?></span></div>
  <div class="info-row"><span class="info-label">Nama Lengkap</span><span class="info-value"><?= e($user['full_name']) ?></span></div>
  <div class="info-row"><span class="info-label">Email</span><span class="info-value"><?= e($user['email'] ?: '-') ?></span></div>
  <div class="info-row"><span class="info-label">No. Telepon</span><span class="info-value"><?= e($user['phone'] ?: '-') ?></span></div>
  <div class="info-row"><span class="info-label">Role</span><span class="info-value"><span class="badge badge-info"><?= e($user['role']) ?></span></span></div>
  <div class="info-row"><span class="info-label">Terdaftar Sejak</span><span class="info-value"><?= e($user['created_at']) ?></span></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
