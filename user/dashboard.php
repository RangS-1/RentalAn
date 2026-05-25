<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
if ($_SESSION['role'] === 'admin') { header('Location: ../admin/dashboard.php'); exit; }

$item = new Item($db);
$rental = new Rental($db);

// Submit pinjam
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow'])) {
    $item_id = (int)$_POST['item_id'];
    $qty = max(1, (int)$_POST['quantity']);
    $itm = $item->getById($item_id);
    if ($itm && $qty <= $itm['stock']) {
        $rental->requestBorrow(
            $_SESSION['user_id'], $item_id,
            trim($_POST['borrower_name']), trim($_POST['borrow_location']),
            max(1, (int)$_POST['duration_days']), $qty
        );
        header('Location: my_rentals.php?borrowed=1'); exit;
    }
}

$items = $item->getAll();
$pageTitle = 'Katalog Barang';
include __DIR__ . '/../includes/header.php';
?>
<h1>📦 Katalog Barang Tersedia</h1>
<?php if (isset($_GET['borrowed'])): ?><div class="alert alert-success">Permintaan pinjam dikirim. Menunggu konfirmasi admin.</div><?php endif; ?>

<div class="grid grid-3">
<?php foreach ($items as $it): ?>
  <div class="item-card">
    <img src="../<?= e($it['image'] ?: 'assets/images/placeholder.png') ?>" alt="<?= e($it['name']) ?>" class="item-img" onerror="this.src='../assets/images/placeholder.png'">
    <div class="item-body">
      <div class="item-title"><?= e($it['name']) ?></div>
      <div class="item-price"><?= rupiah($it['price_per_day']) ?> / hari</div>
      <div class="item-stock">Stok tersedia: <b><?= (int)$it['stock'] ?></b></div>
      <?php if ($it['stock'] > 0): ?>
        <button class="btn btn-primary btn-block" onclick='openBorrowModal(<?= json_encode($it) ?>)'>Pinjam</button>
      <?php else: ?>
        <button class="btn btn-secondary btn-block" disabled>Stok Habis</button>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
<?php if (empty($items)): ?><p>Belum ada barang tersedia.</p><?php endif; ?>
</div>

<!-- Modal Pinjam -->
<div class="modal-overlay" id="borrowModal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('borrowModal')">&times;</button>
    <h2 id="bm-name"></h2>
    <p id="bm-desc" style="color:#6b7280;margin-bottom:14px"></p>
    <p style="margin-bottom:14px"><b id="bm-price"></b> · Stok: <span id="bm-stock"></span></p>
    <form method="post">
      <input type="hidden" name="borrow" value="1">
      <input type="hidden" name="item_id" id="bm-id">
      <div class="form-group"><label>Nama Peminjam</label><input type="text" name="borrower_name" class="form-control" value="<?= e($_SESSION['full_name']) ?>" required></div>
      <div class="form-group"><label>Lokasi Peminjaman</label><input type="text" name="borrow_location" class="form-control" placeholder="cth. Jakarta Selatan" required></div>
      <div class="form-group"><label>Lama Peminjaman (hari)</label><input type="number" name="duration_days" class="form-control" min="1" value="1" required></div>
      <div class="form-group"><label>Jumlah Dipinjam</label><input type="number" name="quantity" id="bm-qty" class="form-control" min="1" value="1" required></div>
      <button class="btn btn-primary btn-block">Ajukan Pinjam</button>
    </form>
  </div>
</div>

<script src="../assets/js/app.js"></script>
<script>
function openBorrowModal(it){
  document.getElementById('bm-name').textContent=it.name;
  document.getElementById('bm-desc').textContent=it.description||'';
  document.getElementById('bm-price').textContent='Rp '+Number(it.price_per_day).toLocaleString('id-ID')+' / hari';
  document.getElementById('bm-stock').textContent=it.stock;
  document.getElementById('bm-id').value=it.id;
  document.getElementById('bm-qty').max=it.stock;
  openModal('borrowModal');
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
