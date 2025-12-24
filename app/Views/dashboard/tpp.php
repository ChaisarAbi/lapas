<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'dashboard';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item active">Dashboard</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tim Pengamat Pemasyarakatan (TPP)</h3>
                </div>
                <div class="card-body">
                    <p>Selamat datang di dashboard Tim Pengamat Pemasyarakatan. Anda bertanggung jawab untuk:</p>
                    <ul>
                        <li>Mengelola kriteria penilaian pembinaan narapidana</li>
                        <li>Mengelola sub-kriteria penilaian</li>
                        <li>Memproses perhitungan Analytic Network Process (ANP)</li>
                        <li>Memvalidasi konsistensi matriks perbandingan</li>
                        <li>Menyimpan bobot final kriteria</li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info-circle"></i> Informasi ANP</h5>
                        <p>Metode Analytic Network Process (ANP) digunakan untuk menentukan bobot kriteria dengan mempertimbangkan interdependensi antar kriteria.</p>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Kriteria</span>
                                    <span class="info-box-number"><?= $totalKriteria ?? 0 ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-layer-group"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Subkriteria</span>
                                    <span class="info-box-number"><?= $totalSubkriteria ?? 0 ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-weight-hanging"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Bobot Terisi</span>
                                    <span class="info-box-number"><?= $kriteriaDenganBobot ?? 0 ?></span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= $persentaseBobot ?? 0 ?>%"></div>
                                    </div>
                                    <span class="progress-description">
                                        <?= $persentaseBobot ?? 0 ?>% dari total
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Periode Aktif</span>
                                    <span class="info-box-number"><?= $periodeAktif ?? date('Y-m') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Detail -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Detail Kriteria dan Bobot</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($progressData)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Kriteria</th>
                                                        <th>Jumlah Subkriteria</th>
                                                        <th>Bobot</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($progressData as $item): ?>
                                                    <tr>
                                                        <td><?= $item['kriteria'] ?></td>
                                                        <td><?= $item['subkriteria'] ?></td>
                                                        <td><?= number_format($item['bobot'], 3) ?></td>
                                                        <td>
                                                            <?php if ($item['bobot'] > 0): ?>
                                                                <span class="badge badge-success">Sudah diisi</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-warning">Belum diisi</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-list fa-3x text-muted"></i>
                                            <p class="mt-2">Belum ada data kriteria</p>
                                            <a href="<?= base_url('tpp/kriteria') ?>" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Tambah Kriteria
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
