<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$rental = new Rental($db);
$userObj = new User($db);
$admins = $userObj->getAllAdmins();

// Submit pengembalian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return'])) {
    $rid = (int)$_POST['rental_id'];
    $r = $rental->getById($rid);
    if ($r && $r['user_id'] == $_SESSION['user_id'] && $r['status'] === 'active') {
        $rental->requestReturn(
            $rid, (int)$_POST['return_admin_id'],
            (float)$_POST['money_paid'], (float)$_POST['money_change'],
            trim($_POST['return_location'])
        );
        header('Location: my_rentals.php?returned=1'); exit;
    }
}

$rentals = $rental->getByUser($_SESSION['user_id']);
$pageTitle = 'Peminjaman Saya';
include __DIR__ . '/../includes/header.php';
?>
<h1>📋 Peminjaman Saya</h1>
<?php if (isset($_GET['borrowed'])): ?><div class="alert alert-success">✅ Permintaan pinjam dikirim. Menunggu konfirmasi admin.</div><?php endif; ?>
<?php if (isset($_GET['returned'])): ?><div class="alert alert-info">📤 Permintaan pengembalian dikirim. Menunggu konfirmasi admin.</div><?php endif; ?>

<?php if (empty($rentals)): ?>
  <div class="card"><p>Belum ada peminjaman. <a href="dashboard.php">Lihat katalog &rarr;</a></p></div>
<?php endif; ?>

<div class="grid grid-3">
<?php foreach ($rentals as $r):
  $total = $r['price_per_day'] * $r['duration_days'] * $r['quantity'];
?>
  <div class="card">
    <img src="../<?= e($r['item_image'] ?: 'assets/images/placeholder.png') ?>" class="item-img" style="margin:-20px -20px 14px;width:calc(100% + 40px)" onerror="this.src='../assets/images/placeholder.png'">
    <h3><?= e($r['item_name']) ?></h3>
    <p style="font-size:.85rem;color:#6b7280;margin:6px 0">
      <?= (int)$r['quantity'] ?> unit × <?= (int)$r['duration_days'] ?> hari · <?= e($r['borrow_location']) ?>
    </p>
    <p style="margin:8px 0"><b>Total:</b> <?= rupiah($total) ?></p>

    <?php if ($r['status'] === 'pending_borrow'): ?>
      <span class="badge badge-warning">⏳ Menunggu konfirmasi pinjam</span>
    <?php elseif ($r['status'] === 'rejected'): ?>
      <span class="badge badge-danger">❌ Ditolak admin</span>
    <?php elseif ($r['status'] === 'active'): ?>
      <div class="countdown" data-deadline="<?= e($r['return_deadline']) ?>">⏱️ ...</div>
      <button class="btn btn-warning btn-block" style="margin-top:10px" onclick='openReturnModal(<?= json_encode($r) ?>, <?= $total ?>)'>Kembalikan Barang</button>
    <?php elseif ($r['status'] === 'pending_return'): ?>
      <span class="badge badge-info">📤 Menunggu konfirmasi pengembalian</span>
    <?php elseif ($r['status'] === 'returned'): ?>
      <span class="badge badge-success">✅ Selesai dikembalikan</span>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>

<!-- Modal Return -->
<div class="modal-overlay" id="returnModal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('returnModal')">&times;</button>
    <h2>Kembalikan Barang</h2>
    <p id="rm-info" style="color:#6b7280;margin-bottom:14px"></p>
    <p style="margin-bottom:14px"><b>Total yang harus dibayar: <span id="rm-total"></span></b></p>
    <form method="post" id="returnForm">
      <input type="hidden" name="return" value="1">
      <input type="hidden" name="rental_id" id="rm-id">
      <div class="form-group"><label>Admin Penerima</label>
        <select name="return_admin_id" class="form-control" required>
          <option value="">-- Pilih admin --</option>
          <?php foreach ($admins as $a): ?>
            <option value="<?= $a['id'] ?>"><?= e($a['full_name']) ?> (<?= e($a['username']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Uang yang Dibayar (Rp)</label>
        <input type="number" name="money_paid" class="form-control" min="0" step="1000" required oninput="calcChange('returnForm')">
      </div>
      <div class="form-group"><label>Kembalian (Rp)</label>
        <input type="number" name="money_change" class="form-control" readonly>
      </div>
      <div class="form-group"><label>Lokasi Pengembalian</label>
        <input type="text" name="return_location" class="form-control" required>
      </div>
      <button class="btn btn-success btn-block">Kirim Pengembalian</button>
    </form>
  </div>
</div>

<script src="../assets/js/app.js"></script>
<script>
function openReturnModal(r,total){
  document.getElementById('rm-info').textContent=r.item_name+' — '+r.quantity+' unit × '+r.duration_days+' hari';
  document.getElementById('rm-total').textContent='Rp '+Number(total).toLocaleString('id-ID');
  document.getElementById('rm-id').value=r.id;
  const f=document.getElementById('returnForm');
  f.dataset.total=total;
  f.money_paid.value='';f.money_change.value='';
  openModal('returnModal');
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
