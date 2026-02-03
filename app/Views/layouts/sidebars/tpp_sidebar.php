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
                
                <!-- KELOLA KRITERIA DAN SUBKRITERIA -->
                <li class="nav-header">KELOLA KRITERIA</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/kriteria') ?>" class="nav-link <?= $activeMenu == 'kriteria' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-list"></i>
                        <p>Kelola Kriteria</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/subkriteria') ?>" class="nav-link <?= $activeMenu == 'subkriteria' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-list-ol"></i>
                        <p>Kelola Subkriteria</p>
                    </a>
                </li>
                
                <!-- ANALYTIC NETWORK PROCESS -->
                <li class="nav-header">ANALYTIC NETWORK PROCESS</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/anp/pairwise-target') ?>" class="nav-link <?= $activeMenu == 'pairwise-target' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-bullseye"></i>
                        <p>Pairwise (Target-First)</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/anp/edges') ?>" class="nav-link <?= $activeMenu == 'edges' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-arrow-right"></i>
                        <p>Kelola Edges</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/anp/pairwise-comparison') ?>" class="nav-link <?= $activeMenu == 'pairwise-comparison' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-project-diagram"></i>
                        <p>Pairwise (Legacy)</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/anp/partial-result') ?>" class="nav-link <?= $activeMenu == 'partial-result' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-eye"></i>
                        <p>Hasil ANP (Parsial)</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/anp') ?>" class="nav-link <?= $activeMenu == 'anp' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Hasil ANP</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('tpp/bobot/matriks') ?>" class="nav-link <?= $activeMenu == 'konsistensi' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-check-circle"></i>
                        <p>Validasi Konsistensi</p>
                    </a>
                </li>
                
            </ul>
        </nav>
    </div>
</aside>
