<?= $this->extend('layouts/dashboard_template') ?>

<?= $this->section('sidebar_menu') ?>
    <li class="nav-item">
        <a href="<?= base_url('admin/dashboard') ?>" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-header">MANAJEMEN</li>
    <li class="nav-item">
        <a href="<?= base_url('admin/users') ?>" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>Manajemen User</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('admin/narapidana') ?>" class="nav-link">
            <i class="nav-icon fas fa-user-injured"></i>
            <p>Data Narapidana</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('admin/perhitungan/topsis') ?>" class="nav-link">
            <i class="nav-icon fas fa-clipboard-list"></i>
            <p>Perhitungan TOPSIS</p>
        </a>
    </li>
    <li class="nav-header">LAPORAN</li>
    <li class="nav-item">
        <a href="<?= base_url('admin/perhitungan/topsis') ?>" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Statistik Ranking</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('admin/perhitungan/cetak') ?>" class="nav-link">
            <i class="nav-icon fas fa-file-pdf"></i>
            <p>Laporan PDF</p>
        </a>
    </li>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item active">Dashboard</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= $totalNarapidana ?? 0 ?></h3>
                    <p>Total Narapidana</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-injured"></i>
                </div>
                <a href="<?= base_url('admin/narapidana') ?>" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= $narapidanaAktif ?? 0 ?></h3>
                    <p>Narapidana Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="<?= base_url('admin/narapidana') ?>" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= $totalUsers ?? 0 ?></h3>
                    <p>User Terdaftar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="<?= base_url('admin/users') ?>" class="small-box-footer">Kelola user <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?= $totalPenilaian ?? 0 ?></h3>
                    <p>Penilaian Periode <?= $periodeAktif ?? date('Y-m') ?></p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <a href="<?= base_url('admin/perhitungan/topsis?periode=' . ($periodeAktif ?? date('Y-m'))) ?>" class="small-box-footer">Lihat hasil <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistik User Berdasarkan Role</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($roleStats)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Role</th>
                                        <th>Jumlah User</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalUsers = $totalUsers ?? 0;
                                    foreach ($roleStats as $role => $count): 
                                        $percentage = $totalUsers > 0 ? round(($count / $totalUsers) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $roleNames = [
                                                'ADMIN' => 'Administrator',
                                                'TPP' => 'Tim Pengamat Pemasyarakatan',
                                                'BIMKEMASWAT' => 'Petugas Bimbingan',
                                                'WALI_PEMASYARAKATAN' => 'Wali Pemasyarakatan',
                                                'KEPALA_LAPAS' => 'Kepala Lapas'
                                            ];
                                            echo $roleNames[$role] ?? $role;
                                            ?>
                                        </td>
                                        <td><?= $count ?></td>
                                        <td>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-<?= 
                                                    $role == 'ADMIN' ? 'danger' : 
                                                    ($role == 'TPP' ? 'info' : 
                                                    ($role == 'BIMKEMASWAT' ? 'success' : 
                                                    ($role == 'KEPALA_LAPAS' ? 'warning' : 'primary')))
                                                ?>" style="width: <?= $percentage ?>%"></div>
                                            </div>
                                            <small><?= $percentage ?>%</small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted"></i>
                            <p class="mt-2">Belum ada data user</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Sistem</h3>
                </div>
                <div class="card-body">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-info-circle"></i> Periode Aktif</h5>
                        <p>Saat ini sistem menggunakan periode penilaian: <strong><?= $periodeAktif ?? date('Y-m') ?></strong></p>
                        <p>Periode ini digunakan untuk:</p>
                        <ul>
                            <li>Input penilaian oleh petugas BIMKEMASWAT</li>
                            <li>Perhitungan ranking TOPSIS</li>
                            <li>Validasi oleh Kepala Lapas</li>
                        </ul>
                    </div>
                    
                    <div class="callout callout-success">
                        <h5><i class="fas fa-check-circle"></i> Status Sistem</h5>
                        <p>Sistem berjalan dengan baik. Semua modul berfungsi normal.</p>
                        <p>Terakhir diperbarui: <?= date('d/m/Y H:i:s') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sistem Pendukung Keputusan Pembinaan Narapidana</h3>
                </div>
                <div class="card-body">
                    <p>Selamat datang di sistem pendukung keputusan untuk pembinaan narapidana. Sistem ini menggunakan metode ANP dan TOPSIS untuk menentukan prioritas pembinaan.</p>
                    <p>Sebagai <strong>Administrator</strong>, Anda memiliki akses penuh untuk:</p>
                    <ul>
                        <li>Mengelola user dan role</li>
                        <li>Mengelola data narapidana</li>
                        <li>Mengatur periode penilaian</li>
                        <li>Melihat seluruh laporan</li>
                    </ul>
                    <p>Gunakan menu di sidebar untuk navigasi.</p>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
