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
                <div class="card-body p-3">
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
                                <span class="info-box-icon bg-info"><i class="fas fa-balance-scale"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Kriteria (Cluster)</span>
                                    <span class="info-box-number"><?= $totalKriteria ?? 0 ?></span>
                                    <a href="<?= base_url('tpp/kriteria') ?>" class="small-box-footer">Kelola <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-list-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Subkriteria (Node)</span>
                                    <span class="info-box-number"><?= $totalSubkriteria ?? 0 ?></span>
                                    <a href="<?= base_url('tpp/subkriteria') ?>" class="small-box-footer">Kelola <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-project-diagram"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Interdependensi</span>
                                    <span class="info-box-number">Matriks ANP</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= $persentaseInterdependensi ?? 0 ?>%"></div>
                                    </div>
                                    <span class="progress-description">
                                        <?= $persentaseInterdependensi ?? 0 ?>% terisi
                                    </span>
                                    <a href="<?= base_url('tpp/anp/input-interdependensi') ?>" class="small-box-footer">Input <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-chart-bar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Hasil ANP</span>
                                    <span class="info-box-number">Bobot Final</span>
                                    <a href="<?= base_url('tpp/anp') ?>" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Aksi Cepat - Analytic Network Process (ANP)</h3>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-info">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-balance-scale fa-3x mb-3"></i>
                                                    <h5>Kelola Kriteria</h5>
                                                    <p>Atur kriteria dan subkriteria penilaian</p>
                                                    <a href="<?= base_url('tpp/kriteria') ?>" class="btn btn-light btn-block">Akses</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                                                    <h5>Validasi Konsistensi</h5>
                                                    <p>Validasi matriks perbandingan</p>
                                                    <a href="<?= base_url('tpp/bobot/matriks') ?>" class="btn btn-light btn-block">Akses</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-project-diagram fa-3x mb-3"></i>
                                                    <h5>Input Interdependensi</h5>
                                                    <p>Input matriks interdependensi ANP</p>
                                                    <a href="<?= base_url('tpp/anp/input-interdependensi') ?>" class="btn btn-light btn-block">Akses</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-primary">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                                    <h5>Hasil ANP</h5>
                                                    <p>Lihat hasil perhitungan ANP</p>
                                                    <a href="<?= base_url('tpp/anp') ?>" class="btn btn-light btn-block">Akses</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                <div class="card-body p-2">
                                    <?php if (!empty($progressData)): ?>
                                        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th style="min-width: 150px;">Kriteria</th>
                                                        <th style="min-width: 120px;">Jumlah Subkriteria</th>
                                                        <th style="min-width: 100px;">Bobot</th>
                                                        <th style="min-width: 100px;">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($progressData as $item): ?>
                                                    <tr>
                                                        <td><?= $item['kriteria'] ?></td>
                                                        <td class="text-center"><?= $item['subkriteria'] ?></td>
                                                        <td class="text-right"><?= number_format($item['bobot'], 3) ?></td>
                                                        <td class="text-center">
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
