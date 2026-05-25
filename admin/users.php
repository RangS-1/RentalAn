<?php
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$u = new User($db);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $u->updateRole((int)$_POST['id'], $_POST['role'] === 'admin' ? 'admin' : 'user');
    header('Location: users.php?msg=updated'); exit;
}
if (isset($_GET['delete'])) {
    if ((int)$_GET['delete'] !== (int)$_SESSION['user_id']) $u->delete((int)$_GET['delete']);
    header('Location: users.php?msg=deleted'); exit;
}

$users = $u->getAll();
$pageTitle = 'Pengguna Terdaftar';
include __DIR__ . '/../includes/header.php';
?>
<h1>👥 Pengguna Terdaftar</h1>
<?php if (isset($_GET['msg'])): ?><div class="alert alert-success">Berhasil <?= e($_GET['msg']) ?>.</div><?php endif; ?>

<div class="card table-wrap"><table class="table">
  <thead><tr><th>Username</th><th>Nama Lengkap</th><th>Email</th><th>Telepon</th><th>Role</th><th>Terdaftar</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($users as $row): ?>
    <tr>
      <td><b><?= e($row['username']) ?></b></td>
      <td><?= e($row['full_name']) ?></td>
      <td><?= e($row['email'] ?: '-') ?></td>
      <td><?= e($row['phone'] ?: '-') ?></td>
      <td>
        <form method="post" style="display:inline-flex;gap:6px;align-items:center">
          <input type="hidden" name="id" value="<?= $row['id'] ?>">
          <select name="role" class="form-control" style="width:auto;padding:4px 8px">
            <option value="user" <?= $row['role']==='user'?'selected':'' ?>>user</option>
            <option value="admin" <?= $row['role']==='admin'?'selected':'' ?>>admin</option>
          </select>
          <button name="update_role" value="1" class="btn btn-sm btn-primary">Set</button>
        </form>
      </td>
      <td><?= e($row['created_at']) ?></td>
      <td>
        <?php if ($row['id'] != $_SESSION['user_id']): ?>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
        <?php else: ?>
          <span style="color:#9ca3af;font-size:.85rem">Anda</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
