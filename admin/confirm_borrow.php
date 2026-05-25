<?php
require_once __DIR__ . '/../includes/config.php';
requireAdmin();
$rental = new Rental($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rid = (int)$_POST['rental_id'];
    if (isset($_POST['confirm'])) {
        $rental->confirmBorrow($rid);
    } elseif (isset($_POST['reject'])) {
        $rental->rejectBorrow($rid);
    }
    header('Location: confirm_borrow.php'); exit;
}

$list = $rental->getPendingBorrows();
$pageTitle = 'Konfirmasi Pinjam';
include __DIR__ . '/../includes/header.php';
?>
<h1>⏳ Konfirmasi Permintaan Pinjam</h1>
<p style="color:#6b7280;margin-bottom:20px">Setelah dikonfirmasi, stok akan berkurang dan hitung mundur dimulai untuk pengguna.</p>

<?php if (empty($list)): ?>
  <div class="card"><p>Tidak ada permintaan pinjam.</p></div>
<?php else: ?>
<div class="card table-wrap"><table class="table">
  <thead><tr><th>Peminjam</th><th>Barang</th><th>Jumlah</th><th>Durasi</th><th>Lokasi</th><th>Total Estimasi</th><th>Diajukan</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($list as $r):
    $total = $r['price_per_day'] * $r['duration_days'] * $r['quantity'];
  ?>
    <tr>
      <td><b><?= e($r['borrower_name']) ?></b><br><small style="color:#6b7280"><?= e($r['username']) ?></small></td>
      <td><?= e($r['item_name']) ?></td>
      <td><?= (int)$r['quantity'] ?></td>
      <td><?= (int)$r['duration_days'] ?> hari</td>
      <td><?= e($r['borrow_location']) ?></td>
      <td><?= rupiah($total) ?></td>
      <td><?= e($r['created_at']) ?></td>
      <td>
        <form method="post" style="display:inline">
          <input type="hidden" name="rental_id" value="<?= $r['id'] ?>">
          <button name="confirm" value="1" class="btn btn-sm btn-success">✓ Setujui</button>
          <button name="reject" value="1" class="btn btn-sm btn-danger" onclick="return confirm('Tolak permintaan ini?')">✗ Tolak</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
