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
                    <h3 class="card-title">Wali Pemasyarakatan</h3>
                </div>
                <div class="card-body">
                    <p>Selamat datang di dashboard Wali Pemasyarakatan. Anda memiliki akses untuk:</p>
                    <ul>
                        <li>Melihat hasil penilaian narapidana</li>
                        <li>Memantau ranking pembinaan</li>
                        <li>Mengakses informasi perkembangan narapidana</li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info-circle"></i> Informasi Hak Akses</h5>
                        <p>Sebagai Wali Pemasyarakatan, Anda hanya memiliki hak akses membaca (read-only) terhadap data hasil penilaian.</p>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Top 5 Ranking Narapidana (Periode <?= $periode ?>)</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($topRanking)): ?>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Nama</th>
                                                    <th>Skor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($topRanking as $index => $item): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= $item['narapidana']['nama_lengkap'] ?> (<?= $item['narapidana']['nomor_registrasi'] ?>)</td>
                                                    <td><?= number_format($item['preferensi'], 3) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <div class="text-center mt-2">
                                            <a href="<?= base_url('wali/ranking') ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-list"></i> Lihat Semua Ranking
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Belum ada data penilaian untuk periode <?= $periode ?>
                                        </div>
                                        <p class="text-muted">Data ranking akan muncul setelah ada input penilaian dari petugas BIMKEMASWAT.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Statistik Pembinaan</h3>
                                </div>
                                <div class="card-body">
                                    <p>Total narapidana: <strong><?= $totalNarapidana ?></strong></p>
                                    <p>Rata-rata skor: <strong><?= number_format($rataRataSkor, 3) ?></strong></p>
                                    <p>Narapidana dengan skor baik (≥0.7): <strong><?= $baikCount ?></strong></p>
                                    <p>Narapidana perlu perhatian (<0.5): <strong><?= $perhatianCount ?></strong></p>
                                    <hr>
                                    <p class="text-muted">
                                        <small><i class="fas fa-info-circle"></i> Data berdasarkan periode <?= $periode ?></small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
