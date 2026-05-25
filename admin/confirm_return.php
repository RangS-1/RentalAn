<?php
require_once __DIR__ . '/../includes/config.php';
requireAdmin();
$rental = new Rental($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $rental->confirmReturn((int)$_POST['rental_id']);
    header('Location: confirm_return.php'); exit;
}

$list = $rental->getPendingReturns();
$pageTitle = 'Konfirmasi Pengembalian';
include __DIR__ . '/../includes/header.php';
?>
<h1>📤 Konfirmasi Pengembalian Barang</h1>
<p style="color:#6b7280;margin-bottom:20px">Setelah dikonfirmasi, stok barang akan bertambah kembali.</p>

<?php if (empty($list)): ?>
  <div class="card"><p>Tidak ada permintaan pengembalian.</p></div>
<?php else: ?>
<div class="card table-wrap"><table class="table">
  <thead><tr><th>Peminjam</th><th>Barang</th><th>Qty</th><th>Admin Penerima</th><th>Uang Bayar</th><th>Kembalian</th><th>Lokasi Kembali</th><th>Diajukan</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($list as $r): ?>
    <tr>
      <td><b><?= e($r['user_full_name']) ?></b><br><small style="color:#6b7280"><?= e($r['username']) ?></small></td>
      <td><?= e($r['item_name']) ?></td>
      <td><?= (int)$r['quantity'] ?></td>
      <td><?= e($r['admin_name'] ?: '-') ?></td>
      <td><?= rupiah($r['money_paid']) ?></td>
      <td><?= rupiah($r['money_change']) ?></td>
      <td><?= e($r['return_location']) ?></td>
      <td><?= e($r['return_requested_at']) ?></td>
      <td>
        <form method="post" style="display:inline">
          <input type="hidden" name="rental_id" value="<?= $r['id'] ?>">
          <button name="confirm" value="1" class="btn btn-sm btn-success">✓ Konfirmasi Kembali</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
