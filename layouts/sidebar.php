<?php $pageNow = $_GET['page'] ?? 'dashboard'; ?>
<div class="sidebar bg-white shadow-sm">
    <div class="p-3 border-bottom">
        <small class="text-muted">Menu Utama</small>
    </div>
    <a class="sidebar-link <?= $pageNow === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a class="sidebar-link <?= $pageNow === 'user' ? 'active' : ''; ?>" href="index.php?page=user">
        <i class="bi bi-people"></i> Manajemen User
    </a>
    <a class="sidebar-link <?= $pageNow === 'pegawai' ? 'active' : ''; ?>" href="index.php?page=pegawai">
        <i class="bi bi-person-badge"></i> Manajemen Pegawai
    </a>
</div>
