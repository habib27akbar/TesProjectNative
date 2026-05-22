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

            <div class="page-header mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Dashboard</h3>
                    <p class="text-muted mb-0">
                        Selamat datang di aplikasi manajemen user dan pegawai.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="dashboard-card card-blue">
                        <div class="dashboard-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <p class="dashboard-label">Total User</p>
                            <h2 class="dashboard-value"><?= e($totalUser); ?></h2>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="dashboard-card card-green">
                        <div class="dashboard-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div>
                            <p class="dashboard-label">Total Pegawai</p>
                            <h2 class="dashboard-value"><?= e($totalPegawai); ?></h2>
                        </div>
                    </div>
                </div>
            </div>

    <?php
            break;

        case 'user':
            echo '<div class="content-card">';
            require_once __DIR__ . '/views/user/index.php';
            echo '</div>';
            break;

        case 'pegawai':
            echo '<div class="content-card">';
            require_once __DIR__ . '/views/pegawai/index.php';
            echo '</div>';
            break;

        default:
            echo '<div class="alert alert-warning shadow-sm border-0">Halaman tidak ditemukan.</div>';
            break;
    }
    ?>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>