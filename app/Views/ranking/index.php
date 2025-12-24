<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'ranking';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url($dashboard_url) ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Ranking Narapidana</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ranking Narapidana</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <form method="get" action="<?= base_url($role == 'WALI_PEMASYARAKATAN' ? 'wali/ranking' : 'kalapas/ranking') ?>" class="form-inline">
                                <select name="periode" class="form-control" onchange="this.form.submit()">
                                    <option value="">Pilih Periode</option>
                                    <?php foreach ($periode_list as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= $periode == $key ? 'selected' : '' ?>><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-warning">
                            <i class="icon fas fa-exclamation-triangle"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($ranking)): ?>
                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i> Menampilkan data untuk periode <strong><?= $periode ?></strong>
                        </div>
                        
                        <div class="row">
                            <?php foreach ($ranking as $index => $item): ?>
                            <div class="col-md-6">
                                <div class="card card-primary card-outline collapsed-card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <span class="badge badge-<?= $index < 3 ? 'success' : ($index < 10 ? 'warning' : 'secondary') ?> mr-2">
                                                #<?= $index + 1 ?>
                                            </span>
                                            <?= $item['narapidana']['nama_lengkap'] ?>
                                            <small class="text-muted">(<?= $item['narapidana']['nomor_registrasi'] ?>)</small>
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <span class="badge badge-light" style="font-size: 1.2em; padding: 5px 10px;">
                                                <?= number_format($item['preferensi'], 3) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="text-muted">
                                                    Kasus: <strong><?= $item['narapidana']['kasus'] ?? '-' ?></strong> |
                                                    Periode: <span class="badge badge-info"><?= $periode ?></span>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-box bg-gradient-success">
                                                    <span class="info-box-icon"><i class="fas fa-plus-circle"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Jarak Positif (D+)</span>
                                                        <span class="info-box-number"><?= number_format($item['d_positif'], 4) ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: <?= min($item['d_positif'] * 100, 100) ?>%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            Mendekati solusi ideal positif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-box bg-gradient-danger">
                                                    <span class="info-box-icon"><i class="fas fa-minus-circle"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Jarak Negatif (D-)</span>
                                                        <span class="info-box-number"><?= number_format($item['d_negatif'], 4) ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: <?= min($item['d_negatif'] * 100, 100) ?>%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            Menjauhi solusi ideal negatif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="callout callout-<?= $item['preferensi'] >= 0.7 ? 'success' : ($item['preferensi'] >= 0.5 ? 'warning' : 'danger') ?>">
                                                    <h5><i class="fas fa-chart-line"></i> Analisis Preferensi</h5>
                                                    <p>
                                                        Nilai Preferensi: <strong><?= number_format($item['preferensi'], 4) ?></strong> |
                                                        Status: 
                                                        <?php if ($item['preferensi'] >= 0.7): ?>
                                                            <span class="badge badge-success">Sangat Baik</span>
                                                        <?php elseif ($item['preferensi'] >= 0.5): ?>
                                                            <span class="badge badge-warning">Cukup Baik</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">Perlu Perhatian</span>
                                                        <?php endif; ?>
                                                    </p>
                                                    <p class="mb-0">
                                                        <small>
                                                            Nilai preferensi mendekati 1 menunjukkan performa yang lebih baik.
                                                            Nilai di atas 0.7 dianggap sangat baik, 0.5-0.7 cukup baik, dan di bawah 0.5 perlu perhatian.
                                                        </small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-2">
                                            <div class="col-md-12 text-right">
                                                <a href="<?= base_url('wali/ranking/detail/' . $item['narapidana']['id']) ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Lihat Detail Lengkap
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Statistik Ranking</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-success">
                                                    <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Top 3</span>
                                                        <span class="info-box-number">3</span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: 100%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            Narapidana terbaik
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-warning">
                                                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Rata-rata</span>
                                                        <span class="info-box-number"><?= number_format(array_sum(array_column($ranking, 'preferensi')) / count($ranking), 4) ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: 70%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            Nilai preferensi rata-rata
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total</span>
                                                        <span class="info-box-number"><?= count($ranking) ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: 100%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            Total narapidana
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-danger">
                                                    <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Perlu Perhatian</span>
                                                        <?php 
                                                        $perhatianCount = 0;
                                                        foreach ($ranking as $item) {
                                                            if ($item['preferensi'] < 0.5) {
                                                                $perhatianCount++;
                                                            }
                                                        }
                                                        ?>
                                                        <span class="info-box-number"><?= $perhatianCount ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: <?= count($ranking) > 0 ? ($perhatianCount / count($ranking)) * 100 : 0 ?>%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            Nilai < 0.5
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($role == 'KEPALA_LAPAS'): ?>
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <a href="<?= base_url('kalapas/ranking/cetak?periode=' . $periode) ?>" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-print"></i> Cetak Laporan
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Data Tidak Tersedia</h5>
                            <p>Tidak ada data ranking untuk periode yang dipilih. Pastikan:</p>
                            <ol>
                                <li>Sudah ada input penilaian dari petugas BIMKEMASWAT</li>
                                <li>Periode yang dipilih sesuai dengan periode penilaian</li>
                                <li>Data kriteria dan bobot sudah diatur oleh TPP</li>
                            </ol>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
