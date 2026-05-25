<?php
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$item = new Item($db);
$action = $_GET['action'] ?? 'list';
$msg = '';

function uploadImage($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) return null;
    $name = 'item_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $target = __DIR__ . '/../assets/images/' . $name;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return 'assets/images/' . $name;
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = (float)$_POST['price_per_day'];
    $stock = (int)$_POST['stock'];
    $img = uploadImage($_FILES['image'] ?? null);

    if (isset($_POST['create'])) {
        $item->create($name, $desc, $price, $stock, $img);
        header('Location: items.php?msg=created'); exit;
    } elseif (isset($_POST['update'])) {
        $item->update((int)$_POST['id'], $name, $desc, $price, $stock, $img);
        header('Location: items.php?msg=updated'); exit;
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    $item->delete((int)$_GET['id']);
    header('Location: items.php?msg=deleted'); exit;
}

$editing = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editing = $item->getById((int)$_GET['id']);
}

$items = $item->getAll();
$pageTitle = 'Kelola Barang';
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
  <h1>📦 Kelola Barang</h1>
  <?php if ($action === 'list'): ?><a href="?action=add" class="btn btn-primary">+ Tambah Barang</a><?php endif; ?>
</div>

<?php if (isset($_GET['msg'])): ?><div class="alert alert-success">Berhasil <?= e($_GET['msg']) ?>.</div><?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
  <div class="card">
    <h2><?= $editing ? 'Edit Barang' : 'Tambah Barang Baru' ?></h2>
    <form method="post" enctype="multipart/form-data" style="margin-top:14px">
      <?php if ($editing): ?>
        <input type="hidden" name="id" value="<?= $editing['id'] ?>">
        <input type="hidden" name="update" value="1">
      <?php else: ?>
        <input type="hidden" name="create" value="1">
      <?php endif; ?>
      <div class="form-group"><label>Nama Barang</label><input type="text" name="name" class="form-control" required value="<?= e($editing['name'] ?? '') ?>"></div>
      <div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control"><?= e($editing['description'] ?? '') ?></textarea></div>
      <div class="form-group"><label>Harga per Hari (Rp)</label><input type="number" name="price_per_day" class="form-control" required min="0" step="1000" value="<?= e($editing['price_per_day'] ?? '') ?>"></div>
      <div class="form-group"><label>Stok</label><input type="number" name="stock" class="form-control" required min="0" value="<?= e($editing['stock'] ?? '0') ?>"></div>
      <div class="form-group"><label>Gambar <?= $editing ? '(kosongkan jika tidak diubah)' : '' ?></label><input type="file" name="image" accept="image/*" class="form-control" <?= $editing ? '' : 'required' ?>>
        <?php if ($editing && $editing['image']): ?><img src="../<?= e($editing['image']) ?>" style="margin-top:10px;max-width:160px;border-radius:6px"><?php endif; ?>
      </div>
      <button class="btn btn-primary"><?= $editing ? 'Simpan Perubahan' : 'Tambah Barang' ?></button>
      <a href="items.php" class="btn btn-secondary">Batal</a>
    </form>
  </div>
<?php else: ?>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Gambar</th><th>Nama</th><th>Harga/Hari</th><th>Stok</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><img src="../<?= e($it['image'] ?: 'assets/images/placeholder.png') ?>" style="width:60px;height:60px;object-fit:cover;border-radius:6px" onerror="this.src='../assets/images/placeholder.png'"></td>
        <td><b><?= e($it['name']) ?></b><br><small style="color:#6b7280"><?= e(mb_strimwidth($it['description'] ?? '', 0, 80, '...')) ?></small></td>
        <td><?= rupiah($it['price_per_day']) ?></td>
        <td><?= (int)$it['stock'] ?></td>
        <td>
          <a href="?action=edit&id=<?= $it['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
          <a href="?action=delete&id=<?= $it['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus barang ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
