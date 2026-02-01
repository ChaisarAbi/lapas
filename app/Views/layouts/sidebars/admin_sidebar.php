<?php
// Sidebar untuk role ADMIN
$activeMenu = $activeMenu ?? 'dashboard';
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url('admin/dashboard') ?>" class="brand-link text-center py-3">
        <div class="brand-logo-container d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-2" style="width: 40px; height: 40px; padding: 5px;">
            <img src="<?= base_url('logo-lapas.png') ?>" alt="Logo Lapas" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <div class="brand-text-container">
            <span class="brand-text font-weight-bold d-block" style="font-size: 1.1rem; letter-spacing: 0.5px;">SPK</span>
            <span class="brand-subtext d-block" style="font-size: 0.75rem; opacity: 0.8;">Pembinaan</span>
        </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= base_url('logo-lapas.png') ?>" class="img-circle elevation-2" alt="User Image" style="width: 33px; height: 33px; object-fit: cover;">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= session()->get('username') ?? 'Admin User' ?></a>
                <small class="text-light">Administrator</small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= $activeMenu == 'dashboard' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <!-- USER MANAGEMENT -->
                <li class="nav-header">MANAJEMEN USER</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/users') ?>" class="nav-link <?= $activeMenu == 'users' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Kelola User</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/users') ?>" class="nav-link <?= $activeMenu == 'users_admin' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Kelola Admin</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/users') ?>" class="nav-link <?= $activeMenu == 'users_tpp' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Kelola TPP</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/users') ?>" class="nav-link <?= $activeMenu == 'users_bimkes' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-md"></i>
                        <p>Kelola BIMKES</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/users') ?>" class="nav-link <?= $activeMenu == 'users_kalapas' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Kelola Kalapas</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/users') ?>" class="nav-link <?= $activeMenu == 'users_wali' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>Kelola Wali</p>
                    </a>
                </li>
                
                <!-- NARAPIDANA MANAGEMENT -->
                <li class="nav-header">MANAJEMEN NARAPIDANA</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/narapidana') ?>" class="nav-link <?= $activeMenu == 'narapidana' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-injured"></i>
                        <p>Kelola Narapidana</p>
                    </a>
                </li>
                
                <!-- PERIODE MANAGEMENT -->
                <li class="nav-header">MANAJEMEN PERIODE</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/periode') ?>" class="nav-link <?= $activeMenu == 'periode' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Kelola Periode</p>
                    </a>
                </li>
                
                <!-- KRITERIA & SUBKRITERIA MANAGEMENT -->
                <li class="nav-header">MANAJEMEN KRITERIA</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/kriteria') ?>" class="nav-link <?= $activeMenu == 'kriteria' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Kelola Kriteria</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/subkriteria') ?>" class="nav-link <?= $activeMenu == 'subkriteria' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-list"></i>
                        <p>Kelola Subkriteria</p>
                    </a>
                </li>
                
                <!-- HASIL ANP -->
                <li class="nav-header">HASIL ANP</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/anp') ?>" class="nav-link <?= $activeMenu == 'anp' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Hasil ANP</p>
                    </a>
                </li>
                
                <!-- PERHITUNGAN - DIHAPUS sesuai permintaan user -->
                <!-- Gunakan manajemen laporan admin untuk perhitungan dan cetak laporan -->
                
                <!-- LAPORAN -->
                <li class="nav-header">LAPORAN</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('admin/laporan') ?>" class="nav-link <?= $activeMenu == 'laporan' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Manajemen Laporan</p>
                    </a>
</li>
                
            </ul>
        </nav>
    </div>
</aside>
