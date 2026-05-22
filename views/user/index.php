<?php
$pesan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $formType = $_POST['form_type'] ?? '';

    if ($nama === '' || $username === '') {
        $error = 'Nama dan username wajib diisi.';
    } else {
        if ($formType === 'create') {
            if ($password === '') {
                $error = 'Password wajib diisi.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama, username, password) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'sss', $nama, $username, $hash);
                $pesan = mysqli_stmt_execute($stmt) ? 'Data user berhasil ditambahkan.' : 'Username sudah digunakan.';
            }
        }

        if ($formType === 'update') {
            $idUpdate = (int)($_POST['id'] ?? 0);
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($koneksi, "UPDATE users SET nama=?, username=?, password=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'sssi', $nama, $username, $hash, $idUpdate);
            } else {
                $stmt = mysqli_prepare($koneksi, "UPDATE users SET nama=?, username=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, 'ssi', $nama, $username, $idUpdate);
            }
            $pesan = mysqli_stmt_execute($stmt) ? 'Data user berhasil diperbarui.' : 'Gagal memperbarui user.';
        }
    }
}

$aksi = $_GET['aksi'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($aksi === 'hapus' && $id > 0) {
    if ($id === (int)($_SESSION['user_id'] ?? 0)) {
        $error = 'User yang sedang login tidak boleh dihapus.';
    } else {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $pesan = mysqli_stmt_execute($stmt) ? 'Data user berhasil dihapus.' : 'Gagal menghapus user.';
    }
}

$result = mysqli_query($koneksi, "SELECT id, nama, username, created_at FROM users ORDER BY id DESC");
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"> <i class="bi bi-people me-2"></i> Manajemen User</h3>
        <small class="text-muted">Kelola data pengguna aplikasi</small>

    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        <i class="bi bi-plus-circle me-1"></i> Tambah User
    </button>
</div>

<?php if ($pesan): ?><div class="alert alert-success"><?= e($pesan); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error); ?></div><?php endif; ?>

<div class="card shadow-sm border-0">

    <div class="card-body table-responsive">
        <table id="tableUser" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Dibuat</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data user.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($users as $no => $row): ?>
                    <tr>
                        <td><?= $no + 1; ?></td>
                        <td><?= e($row['nama']); ?></td>
                        <td><span class="badge bg-light text-dark border"><?= e($row['username']); ?></span></td>
                        <td><?= e($row['created_at']); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditUser<?= e($row['id']); ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')" href="index.php?page=user&aksi=hapus&id=<?= e($row['id']); ?>">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="post">
                <input type="hidden" name="form_type" value="create">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahUserLabel"><i class="bi bi-person-plus me-1"></i> Tambah User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($users as $row): ?>
    <!-- Modal Edit User -->
    <div class="modal fade" id="modalEditUser<?= e($row['id']); ?>" tabindex="-1" aria-labelledby="modalEditUserLabel<?= e($row['id']); ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="post">
                    <input type="hidden" name="form_type" value="update">
                    <input type="hidden" name="id" value="<?= e($row['id']); ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditUserLabel<?= e($row['id']); ?>"><i class="bi bi-pencil-square me-1"></i> Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?= e($row['nama']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= e($row['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <small class="text-muted">kosongkan jika tidak diubah</small></label>
                            <input type="password" name="password" class="form-control" placeholder="Password baru">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<link rel="stylesheet"
    href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tableUser').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                },
                zeroRecords: "Data tidak ditemukan",
                infoEmpty: "Data kosong"
            }
        });
    });
</script>