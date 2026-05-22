<?php $pageNow = $_GET['page'] ?? 'dashboard'; ?>

<aside class="sidebar d-none d-lg-block">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="bi bi-grid-1x2-fill"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold">Menu Utama</h6>
            <small class="text-muted">Navigasi sistem</small>
        </div>
    </div>

    <div class="sidebar-menu">
        <a class="sidebar-link <?= $pageNow === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a class="sidebar-link <?= $pageNow === 'user' ? 'active' : ''; ?>" href="index.php?page=user">
            <i class="bi bi-people"></i>
            <span>Manajemen User</span>
        </a>

        <a class="sidebar-link <?= $pageNow === 'pegawai' ? 'active' : ''; ?>" href="index.php?page=pegawai">
            <i class="bi bi-person-badge"></i>
            <span>Manajemen Pegawai</span>
        </a>
    </div>
</aside>

<div class="offcanvas offcanvas-start mobile-sidebar" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title fw-bold mb-0">Menu Utama</h5>
            <small class="text-muted">Navigasi sistem</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0">
        <div class="sidebar-menu">
            <a class="sidebar-link <?= $pageNow === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <a class="sidebar-link <?= $pageNow === 'user' ? 'active' : ''; ?>" href="index.php?page=user">
                <i class="bi bi-people"></i>
                <span>Manajemen User</span>
            </a>

            <a class="sidebar-link <?= $pageNow === 'pegawai' ? 'active' : ''; ?>" href="index.php?page=pegawai">
                <i class="bi bi-person-badge"></i>
                <span>Manajemen Pegawai</span>
            </a>
        </div>
    </div>
</div>