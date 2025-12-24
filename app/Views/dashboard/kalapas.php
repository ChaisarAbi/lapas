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
                    <h3 class="card-title">Kepala Lembaga Pemasyarakatan</h3>
                </div>
                <div class="card-body">
                    <p>Selamat datang di dashboard Kepala Lembaga Pemasyarakatan. Anda memiliki akses untuk:</p>
                    <ul>
                        <li>Melihat hasil penilaian akhir narapidana</li>
                        <li>Menyetujui hasil akhir perankingan</li>
                        <li>Mencetak laporan PDF untuk dokumentasi</li>
                        <li>Melakukan analisis trend pembinaan</li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Tanggung Jawab</h5>
                        <p>Sebagai Kepala Lapas, Anda adalah pengambil keputusan akhir dalam proses pembinaan narapidana.</p>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= $menungguValidasi ?></h3>
                                    <p>Menunggu Validasi</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <a href="<?= base_url('kalapas/validasi') ?>" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= $tervalidasi ?></h3>
                                    <p>Tervalidasi</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <a href="<?= base_url('kalapas/hasil') ?>" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?= $totalValidasi ?? 0 ?></h3>
                                    <p>Hasil Validasi</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <a href="<?= base_url('kalapas/hasil-validasi') ?>" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?= $perluTindakan ?></h3>
                                    <p>Perlu Tindakan</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <a href="<?= base_url('kalapas/ranking') ?>" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> Statistik berdasarkan periode <strong><?= $periode ?></strong>. Total narapidana: <strong><?= $totalNarapidana ?></strong>, Total penilaian: <strong><?= $totalPenilaian ?></strong>.
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Laporan Terbaru</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="fas fa-file-pdf text-danger mr-2"></i>
                                            Laporan Bulanan Desember 2025
                                            <span class="float-right">
                                                <a href="#" class="btn btn-sm btn-primary">Cetak</a>
                                            </span>
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-file-excel text-success mr-2"></i>
                                            Data Ranking Narapidana
                                            <span class="float-right">
                                                <a href="#" class="btn btn-sm btn-success">Export</a>
                                            </span>
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-chart-bar text-warning mr-2"></i>
                                            Analisis Trend Pembinaan
                                            <span class="float-right">
                                                <a href="#" class="btn btn-sm btn-info">Lihat</a>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Aksi Cepat</h3>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="<?= base_url('kalapas/validasi') ?>" class="btn btn-success btn-lg">
                                            <i class="fas fa-check-circle mr-2"></i> Validasi Hasil
                                        </a>
                                        <a href="<?= base_url('kalapas/ranking/cetak') ?>" target="_blank" class="btn btn-primary btn-lg">
                                            <i class="fas fa-file-pdf mr-2"></i> Cetak Laporan
                                        </a>
                                        <a href="<?= base_url('kalapas/hasil') ?>" class="btn btn-info btn-lg">
                                            <i class="fas fa-chart-line mr-2"></i> Analisis Data
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
