<?php
// Sidebar untuk role TPP (Tim Pengamat Pemasyarakatan)
$activeMenu = $activeMenu ?? 'dashboard';
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url('tpp/dashboard') ?>" class="brand-link text-center py-3">
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
                <a href="#" class="d-block"><?= session()->get('username') ?? 'TPP User' ?></a>
                <small class="text-light">Tim Pengamat Pemasyarakatan</small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= base_url('tpp/dashboard') ?>" class="nav-link <?= $activeMenu == 'dashboard' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <!-- ANALYTIC NETWORK PROCESS -->
                <li class="nav-header">ANALYTIC NETWORK PROCESS</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/kriteria') ?>" class="nav-link <?= $activeMenu == 'kriteria' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-balance-scale"></i>
                        <p>Kelola Kriteria</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/subkriteria') ?>" class="nav-link <?= $activeMenu == 'subkriteria' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Kelola Subkriteria</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/periode') ?>" class="nav-link <?= $activeMenu == 'periode' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Kelola Periode</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/bobot') ?>" class="nav-link <?= $activeMenu == 'bobot' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calculator"></i>
                        <p>Input Bobot</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/bobot/matriks') ?>" class="nav-link <?= $activeMenu == 'konsistensi' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-check-circle"></i>
                        <p>Validasi Konsistensi</p>
                    </a>
                </li>
                
                <!-- LAPORAN -->
                <li class="nav-header">LAPORAN</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/anp') ?>" class="nav-link <?= $activeMenu == 'anp' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Hasil ANP</p>
                    </a>
                </li>
                
               
            </ul>
        </nav>
    </div>
</aside>
