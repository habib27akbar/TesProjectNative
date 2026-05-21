<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/helpers/auth.php';

$page = $_GET['page'] ?? 'dashboard';

if ($page === 'login') {
    require_once __DIR__ . '/views/auth/login.php';
    exit;
}

checkLogin();
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/layouts/navbar.php';
require_once __DIR__ . '/layouts/sidebar.php';
?>

<main class="content-wrapper">
<?php
switch ($page) {
    case 'dashboard':
        $totalUser = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users"))['total'];
        $totalPegawai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pegawai"))['total'];
        ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Dashboard</h3>
                <p class="text-muted mb-0">Selamat datang di aplikasi manajemen user dan pegawai.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle p-3 me-3"><i class="bi bi-people fs-3"></i></div>
                        <div>
                            <p class="text-muted mb-1">Total User</p>
                            <h2 class="fw-bold mb-0"><?= e($totalUser); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle p-3 me-3"><i class="bi bi-person-badge fs-3"></i></div>
                        <div>
                            <p class="text-muted mb-1">Total Pegawai</p>
                            <h2 class="fw-bold mb-0"><?= e($totalPegawai); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    case 'user':
        require_once __DIR__ . '/views/user/index.php';
        break;
    case 'pegawai':
        require_once __DIR__ . '/views/pegawai/index.php';
        break;
    default:
        echo '<div class="alert alert-warning">Halaman tidak ditemukan.</div>';
        break;
}
?>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
