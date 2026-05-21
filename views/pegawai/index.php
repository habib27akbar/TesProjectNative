<?php



function upload_foto($file)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload foto gagal.');
    }

    $maxSize = 300 * 1024;

    if ($file['size'] > $maxSize) {
        throw new Exception('Ukuran foto maksimal 300KB.');
    }

    $allowedMime = ['image/jpeg'];
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $file['tmp_name']);
    finfo_close($fileInfo);

    if (!in_array($mimeType, $allowedMime)) {
        throw new Exception('Format foto harus JPG/JPEG.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['jpg', 'jpeg'])) {
        throw new Exception('Ekstensi foto harus JPG/JPEG.');
    }

    $uploadDir = __DIR__ . '/../../assets/uploads/pegawai/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $namaFile = 'pegawai_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $tujuan = $uploadDir . $namaFile;

    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        throw new Exception('Gagal menyimpan foto.');
    }

    return $namaFile;
}

$success = '';
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $aksi = $_POST['aksi'] ?? '';

        $nama_pegawai = trim($_POST['nama_pegawai'] ?? '');
        $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $no_hp = trim($_POST['no_hp'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $kode_pos = trim($_POST['kode_pos'] ?? '');
        $provinsi = trim($_POST['provinsi'] ?? '');
        $kabupaten = trim($_POST['kabupaten'] ?? '');
        $kecamatan = trim($_POST['kecamatan'] ?? '');
        $kelurahan = trim($_POST['kelurahan'] ?? '');

        if ($nama_pegawai === '') {
            throw new Exception('Nama pegawai wajib diisi.');
        }

        if (!in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) {
            throw new Exception('Jenis kelamin tidak valid.');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format email tidak valid.');
        }
        $id_provinsi  = $_POST['id_provinsi'] ?? null;
        $id_kabupaten = $_POST['id_kabupaten'] ?? null;
        $id_kecamatan = $_POST['id_kecamatan'] ?? null;
        $id_kelurahan = $_POST['id_kelurahan'] ?? null;
        if ($aksi === 'tambah') {
            $foto = upload_foto($_FILES['foto'] ?? null);

            if (!$foto) {
                throw new Exception('Foto pegawai wajib diupload.');
            }

            $stmt = $koneksi->prepare("
    INSERT INTO pegawai 
    (
        nama_pegawai,
        jenis_kelamin,
        email,
        no_hp,
        foto,
        alamat,
        kode_pos,
        id_provinsi,
        id_kabupaten,
        id_kecamatan,
        id_kelurahan
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

            $stmt->bind_param(
                "sssssssiiii",
                $nama_pegawai,
                $jenis_kelamin,
                $email,
                $no_hp,
                $foto,
                $alamat,
                $kode_pos,
                $id_provinsi,
                $id_kabupaten,
                $id_kecamatan,
                $id_kelurahan
            );

            $stmt->execute();

            $success = 'Data pegawai berhasil ditambahkan.';
        }

        if ($aksi === 'edit') {
            $id = (int) ($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID pegawai tidak valid.');
            }

            $stmtOld = $koneksi->prepare("SELECT foto FROM pegawai WHERE id = ?");
            $stmtOld->bind_param("i", $id);
            $stmtOld->execute();
            $oldData = $stmtOld->get_result()->fetch_assoc();

            if (!$oldData) {
                throw new Exception('Data pegawai tidak ditemukan.');
            }

            $fotoBaru = upload_foto($_FILES['foto'] ?? null);

            if ($fotoBaru) {
                $stmt = $koneksi->prepare("
    UPDATE pegawai SET
        nama_pegawai = ?,
        jenis_kelamin = ?,
        email = ?,
        no_hp = ?,
        foto = ?,
        alamat = ?,
        kode_pos = ?,
        id_provinsi = ?,
        id_kabupaten = ?,
        id_kecamatan = ?,
        id_kelurahan = ?
    WHERE id = ?
");

                $stmt->bind_param(
                    "sssssssiiiii",
                    $nama_pegawai,
                    $jenis_kelamin,
                    $email,
                    $no_hp,
                    $fotoBaru,
                    $alamat,
                    $kode_pos,
                    $id_provinsi,
                    $id_kabupaten,
                    $id_kecamatan,
                    $id_kelurahan,
                    $id
                );

                $stmt->execute();

                if (!empty($oldData['foto'])) {
                    $oldPath = __DIR__ . '/../../assets/uploads/pegawai/' . $oldData['foto'];

                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            } else {
                $stmt = $koneksi->prepare("
    UPDATE pegawai SET
        nama_pegawai = ?,
        jenis_kelamin = ?,
        email = ?,
        no_hp = ?,
        alamat = ?,
        kode_pos = ?,
        id_provinsi = ?,
        id_kabupaten = ?,
        id_kecamatan = ?,
        id_kelurahan = ?
    WHERE id = ?
");

                $stmt->bind_param(
                    "ssssssiiiii",
                    $nama_pegawai,
                    $jenis_kelamin,
                    $email,
                    $no_hp,
                    $alamat,
                    $kode_pos,
                    $id_provinsi,
                    $id_kabupaten,
                    $id_kecamatan,
                    $id_kelurahan,
                    $id
                );

                $stmt->execute();
            }

            $success = 'Data pegawai berhasil diupdate.';
        }
    }

    if (isset($_GET['hapus'])) {
        $id = (int) $_GET['hapus'];

        $stmtOld = $koneksi->prepare("SELECT foto FROM pegawai WHERE id = ?");
        $stmtOld->bind_param("i", $id);
        $stmtOld->execute();
        $oldData = $stmtOld->get_result()->fetch_assoc();

        if ($oldData) {
            $stmtDelete = $koneksi->prepare("DELETE FROM pegawai WHERE id = ?");
            $stmtDelete->bind_param("i", $id);
            $stmtDelete->execute();

            if (!empty($oldData['foto'])) {
                $oldPath = __DIR__ . '/../../assets/uploads/pegawai/' . $oldData['foto'];

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $success = 'Data pegawai berhasil dihapus.';
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

$result = $koneksi->query("
    SELECT 
        pegawai.*,
        provinsi.name AS provinsi,
        kabupaten.name AS kabupaten,
        kecamatan.name AS kecamatan,
        kelurahan.name AS kelurahan
    FROM pegawai
    LEFT JOIN provinsi 
        ON provinsi.id = pegawai.id_provinsi
    LEFT JOIN kabupaten 
        ON kabupaten.id = pegawai.id_kabupaten
    LEFT JOIN kecamatan 
        ON kecamatan.id = pegawai.id_kecamatan
    LEFT JOIN kelurahan 
        ON kelurahan.id = pegawai.id_kelurahan
    ORDER BY pegawai.id DESC
");
$pegawai = [];

while ($row = $result->fetch_assoc()) {
    $pegawai[] = $row;
}

$provinsiResult = $koneksi->query("SELECT * FROM provinsi ORDER BY name ASC");

$provinsi = [];

while ($row = $provinsiResult->fetch_assoc()) {
    $provinsi[] = $row;
}


?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">
            <i class="bi bi-person-badge me-2"></i>

            Manajemen Pegawai
        </h3>
        <small class="text-muted">Kelola data pegawai</small>
    </div>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-2"></i>
        Tambah Pegawai
    </button>
</div>

<?php if ($success) : ?>
    <div class="alert alert-success">
        <?= e($success); ?>
    </div>
<?php endif; ?>

<?php if ($error) : ?>
    <div class="alert alert-danger">
        <?= e($error); ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablePegawai" class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Wilayah</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($pegawai as $p) :
                        $kabupatenEdit = [];
                        $kecamatanEdit = [];
                        $kelurahanEdit = [];

                        if (!empty($p['id_provinsi'])) {
                            $stmtKab = $koneksi->prepare("SELECT * FROM kabupaten WHERE province_id = ? ORDER BY name ASC");
                            $stmtKab->bind_param("i", $p['id_provinsi']);
                            $stmtKab->execute();
                            $resultKab = $stmtKab->get_result();

                            while ($row = $resultKab->fetch_assoc()) {
                                $kabupatenEdit[] = $row;
                            }
                        }

                        if (!empty($p['id_kabupaten'])) {
                            $stmtKec = $koneksi->prepare("SELECT * FROM kecamatan WHERE regency_id = ? ORDER BY name ASC");
                            $stmtKec->bind_param("i", $p['id_kabupaten']);
                            $stmtKec->execute();
                            $resultKec = $stmtKec->get_result();

                            while ($row = $resultKec->fetch_assoc()) {
                                $kecamatanEdit[] = $row;
                            }
                        }

                        if (!empty($p['id_kecamatan'])) {
                            $stmtKel = $koneksi->prepare("SELECT * FROM kelurahan WHERE district_id = ? ORDER BY name ASC");
                            $stmtKel->bind_param("i", $p['id_kecamatan']);
                            $stmtKel->execute();
                            $resultKel = $stmtKel->get_result();

                            while ($row = $resultKel->fetch_assoc()) {
                                $kelurahanEdit[] = $row;
                            }
                        }
                    ?>
                        <tr>
                            <td width="100">
                                <?php if (!empty($p['foto'])) : ?>
                                    <img src="assets/uploads/pegawai/<?= e($p['foto']); ?>"
                                        class="rounded shadow-sm"
                                        width="70"
                                        height="70"
                                        style="object-fit: cover;">
                                <?php else : ?>
                                    <span class="badge bg-secondary">Tidak ada</span>
                                <?php endif; ?>
                            </td>

                            <td><?= e($p['nama_pegawai']); ?></td>
                            <td><?= e($p['jenis_kelamin']); ?></td>
                            <td><?= e($p['email']); ?></td>
                            <td><?= e($p['no_hp']); ?></td>

                            <td>
                                <?= e($p['provinsi']); ?><br>
                                <?= e($p['kabupaten']); ?><br>
                                <?= e($p['kecamatan']); ?><br>
                                <?= e($p['kelurahan']); ?>
                            </td>

                            <td>
                                <button class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit<?= $p['id']; ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <a href="index.php?page=pegawai&hapus=<?= $p['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus pegawai ini?')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit<?= $p['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="aksi" value="edit">
                                        <input type="hidden" name="id" value="<?= $p['id']; ?>">

                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Pegawai</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nama Pegawai</label>
                                                    <input type="text"
                                                        name="nama_pegawai"
                                                        class="form-control"
                                                        value="<?= e($p['nama_pegawai']); ?>"
                                                        required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Jenis Kelamin</label>
                                                    <select name="jenis_kelamin" class="form-select" required>
                                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                                        <option value="Laki-laki" <?= $p['jenis_kelamin'] === 'Laki-laki' ? 'selected' : ''; ?>>
                                                            Laki-laki
                                                        </option>
                                                        <option value="Perempuan" <?= $p['jenis_kelamin'] === 'Perempuan' ? 'selected' : ''; ?>>
                                                            Perempuan
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email"
                                                        name="email"
                                                        class="form-control"
                                                        value="<?= e($p['email']); ?>">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">No HP</label>
                                                    <input type="text"
                                                        name="no_hp"
                                                        class="form-control"
                                                        value="<?= e($p['no_hp']); ?>">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Foto</label>
                                                    <input type="file"
                                                        name="foto"
                                                        class="form-control foto-upload"
                                                        accept=".jpg,.jpeg">
                                                    <small class="text-muted">
                                                        Kosongkan jika tidak ingin mengubah foto. Maksimal 300KB.
                                                    </small>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Kode Pos</label>
                                                    <input type="text"
                                                        name="kode_pos"
                                                        class="form-control"
                                                        value="<?= e($p['kode_pos']); ?>">
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Alamat</label>
                                                    <textarea name="alamat"
                                                        class="form-control"
                                                        rows="3"><?= e($p['alamat']); ?></textarea>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Provinsi</label>
                                                    <select name="id_provinsi"
                                                        class="form-select provinsi-edit"
                                                        data-id="<?= $p['id']; ?>"
                                                        required>

                                                        <option value="">-- Pilih Provinsi --</option>

                                                        <?php foreach ($provinsi as $pr) : ?>
                                                            <option value="<?= $pr['id']; ?>"
                                                                <?= $pr['id'] == $p['id_provinsi'] ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($pr['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>

                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Kabupaten</label>
                                                    <select name="id_kabupaten"
                                                        id="kabupaten-edit<?= $p['id']; ?>"
                                                        class="form-select"
                                                        required>

                                                        <option value="">-- Pilih Kabupaten --</option>

                                                        <?php foreach ($kabupatenEdit as $kb) : ?>
                                                            <option value="<?= $kb['id']; ?>"
                                                                <?= $kb['id'] == $p['id_kabupaten'] ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($kb['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>

                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Kecamatan</label>
                                                    <select name="id_kecamatan"
                                                        id="kecamatan-edit<?= $p['id']; ?>"
                                                        class="form-select"
                                                        required>

                                                        <option value="">-- Pilih Kecamatan --</option>

                                                        <?php foreach ($kecamatanEdit as $kc) : ?>
                                                            <option value="<?= $kc['id']; ?>"
                                                                <?= $kc['id'] == $p['id_kecamatan'] ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($kc['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>

                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Kelurahan</label>
                                                    <select name="id_kelurahan"
                                                        id="kelurahan-edit<?= $p['id']; ?>"
                                                        class="form-select"
                                                        required>

                                                        <option value="">-- Pilih Kelurahan --</option>

                                                        <?php foreach ($kelurahanEdit as $kl) : ?>
                                                            <option value="<?= $kl['id']; ?>"
                                                                <?= $kl['id'] == $p['id_kelurahan'] ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($kl['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button"
                                                class="btn btn-light"
                                                data-bs-dismiss="modal">
                                                Batal
                                            </button>

                                            <button type="submit" class="btn btn-primary">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="aksi" value="tambah">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pegawai</label>
                            <input type="text"
                                name="nama_pegawai"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                name="email"
                                class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">No HP</label>
                            <input type="text"
                                name="no_hp"
                                class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file"
                                name="foto"
                                class="form-control foto-upload"
                                accept=".jpg,.jpeg"
                                required>
                            <small class="text-muted">
                                Format JPG/JPEG, maksimal 300KB.
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Pos</label>
                            <input type="text"
                                name="kode_pos"
                                class="form-control">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat"
                                class="form-control"
                                rows="3"></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Provinsi</label>
                            <select name="id_provinsi" id="provinsi" class="form-select" required>
                                <option value="">-- Pilih Provinsi --</option>

                                <?php foreach ($provinsi as $pr) : ?>
                                    <option value="<?= $pr['id']; ?>">
                                        <?= htmlspecialchars($pr['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kabupaten</label>
                            <select name="id_kabupaten" id="kabupaten" class="form-select" required>
                                <option value="">-- Pilih Kabupaten --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kecamatan</label>
                            <select name="id_kecamatan" id="kecamatan" class="form-select" required>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelurahan</label>
                            <select name="id_kelurahan" id="kelurahan" class="form-select" required>
                                <option value="">-- Pilih Kelurahan --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet"
    href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tablePegawai').DataTable({
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

    document.querySelectorAll('.foto-upload').forEach(function(input) {
        input.addEventListener('change', function() {
            const file = this.files[0];

            if (!file) {
                return;
            }

            const allowedTypes = ['image/jpeg'];

            if (!allowedTypes.includes(file.type)) {
                alert('Format foto harus JPG/JPEG');
                this.value = '';
                return;
            }

            const maxSize = 300 * 1024;

            if (file.size > maxSize) {
                alert('Ukuran foto maksimal 300KB');
                this.value = '';
                return;
            }
        });
    });

    document.getElementById('provinsi').addEventListener('change', function() {
        fetch('ajax/get_kabupaten.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + this.value
            })
            .then(response => response.json())
            .then(data => {
                let kabupaten = document.getElementById('kabupaten');
                let kecamatan = document.getElementById('kecamatan');
                let kelurahan = document.getElementById('kelurahan');

                kabupaten.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
                kecamatan.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                kelurahan.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                data.forEach(item => {
                    kabupaten.innerHTML += `
                <option value="${item.id}">
                    ${item.name}
                </option>
            `;
                });
            });
    });

    document.getElementById('kabupaten').addEventListener('change', function() {
        fetch('ajax/get_kecamatan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + this.value
            })
            .then(response => response.json())
            .then(data => {
                let kecamatan = document.getElementById('kecamatan');
                let kelurahan = document.getElementById('kelurahan');

                kecamatan.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                kelurahan.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                data.forEach(item => {
                    kecamatan.innerHTML += `
                <option value="${item.id}">
                    ${item.name}
                </option>
            `;
                });
            });
    });

    document.getElementById('kecamatan').addEventListener('change', function() {
        fetch('ajax/get_kelurahan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + this.value
            })
            .then(response => response.json())
            .then(data => {
                let kelurahan = document.getElementById('kelurahan');

                kelurahan.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                data.forEach(item => {
                    kelurahan.innerHTML += `
                <option value="${item.id}">
                    ${item.name}
                </option>
            `;
                });
            });
    });

    document.querySelectorAll('.provinsi-edit').forEach(function(element) {
        element.addEventListener('change', function() {
            let id = this.dataset.id;

            fetch('ajax/get_kabupaten.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + this.value
                })
                .then(response => response.json())
                .then(data => {
                    let kabupaten = document.getElementById('kabupaten-edit' + id);
                    let kecamatan = document.getElementById('kecamatan-edit' + id);
                    let kelurahan = document.getElementById('kelurahan-edit' + id);

                    kabupaten.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
                    kecamatan.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                    kelurahan.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                    data.forEach(item => {
                        kabupaten.innerHTML += `
                    <option value="${item.id}">
                        ${item.name}
                    </option>
                `;
                    });
                });
        });
    });

    document.querySelectorAll('[id^="kabupaten-edit"]').forEach(function(element) {
        element.addEventListener('change', function() {
            let id = this.id.replace('kabupaten-edit', '');

            fetch('ajax/get_kecamatan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + this.value
                })
                .then(response => response.json())
                .then(data => {
                    let kecamatan = document.getElementById('kecamatan-edit' + id);
                    let kelurahan = document.getElementById('kelurahan-edit' + id);

                    kecamatan.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                    kelurahan.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                    data.forEach(item => {
                        kecamatan.innerHTML += `
                    <option value="${item.id}">
                        ${item.name}
                    </option>
                `;
                    });
                });
        });
    });

    document.querySelectorAll('[id^="kecamatan-edit"]').forEach(function(element) {
        element.addEventListener('change', function() {
            let id = this.id.replace('kecamatan-edit', '');

            fetch('ajax/get_kelurahan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + this.value
                })
                .then(response => response.json())
                .then(data => {
                    let kelurahan = document.getElementById('kelurahan-edit' + id);

                    kelurahan.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                    data.forEach(item => {
                        kelurahan.innerHTML += `
                    <option value="${item.id}">
                        ${item.name}
                    </option>
                `;
                    });
                });
        });
    });
</script>