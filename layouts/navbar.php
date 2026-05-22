<nav class="navbar navbar-expand-lg navbar-dark app-navbar fixed-top shadow-sm">
    <div class="container-fluid px-3 px-lg-4">

        <button class="btn btn-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
            <i class="bi bi-list fs-5"></i>
        </button>

        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php?page=dashboard">
            <i class="bi bi-building-check"></i>
            <span>Manajemen Pegawai</span>
        </a>

        <div class="ms-auto">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span class="d-none d-sm-inline"><?= e($_SESSION['nama'] ?? 'Admin'); ?></span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li>
                        <span class="dropdown-item-text small text-muted">
                            Login sebagai<br>
                            <strong><?= e($_SESSION['role'] ?? 'Admin'); ?></strong>
                        </span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</nav>