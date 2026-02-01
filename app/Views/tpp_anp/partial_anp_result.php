<?= $this->extend('layouts/dashboard_template') ?>

<?= $this->section('sidebar_menu') ?>
    <li class="nav-item">
        <a href="<?= base_url('tpp/dashboard') ?>" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-header">ANALYTIC NETWORK PROCESS</li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/anp/pairwise-comparison') ?>" class="nav-link">
            <i class="nav-icon fas fa-project-diagram"></i>
            <p>Pairwise Comparison</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/anp/partial-result') ?>" class="nav-link active">
            <i class="nav-icon fas fa-eye"></i>
            <p>Hasil ANP (Parsial)</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/anp') ?>" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Hasil ANP</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/bobot/matriks') ?>" class="nav-link">
            <i class="nav-icon fas fa-check-circle"></i>
            <p>Validasi Konsistensi</p>
        </a>
    </li>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/anp') ?>">Hasil ANP</a></li>
    <li class="breadcrumb-item active">Hasil ANP Parsial</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hasil ANP Parsial (Dengan Data Default)</h3>
                    <div class="card-tools">
                        <?php if ($periode): ?>
                            <span class="badge badge-info">Periode: <?= $periode['nama_periode'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check"></i> <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-ban"></i> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($warning)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <?= $warning ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($hasilAnp)): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Statistik Perhitungan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box bg-light">
                                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Jumlah Node</span>
                                                    <span class="info-box-number"><?= $hasilAnp['n'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box bg-light">
                                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Konsisten</span>
                                                    <span class="info-box-number">
                                                        <?= $hasilAnp['konsisten'] ? 'Ya' : 'Tidak' ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="info-box bg-light">
                                                <span class="info-box-icon bg-warning"><i class="fas fa-percentage"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">CR</span>
                                                    <span class="info-box-number"><?= number_format($hasilAnp['cr'], 4) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box bg-light">
                                                <span class="info-box-icon bg-primary"><i class="fas fa-calculator"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Bobot</span>
                                                    <span class="info-box-number"><?= number_format($hasilAnp['total_bobot_akhir'], 4) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Ringkasan Bobot</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Subkriteria</th>
                                                    <th class="text-right">Bobot</th>
                                                    <th class="text-right">%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($hasilAnp['bobot'] as $index => $item): ?>
                                                <tr>
                                                    <td><?= $item['nama'] ?></td>
                                                    <td class="text-right"><?= number_format($item['weight'], 4) ?></td>
                                                    <td class="text-right"><?= number_format($item['weight'] * 100, 2) ?>%</td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td>Total</td>
                                                    <td class="text-right"><?= number_format($hasilAnp['total_bobot_akhir'], 4) ?></td>
                                                    <td class="text-right">100.00%</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Informasi:</h6>
                                <p>Hasil ANP ini menggunakan data default (semua pairwise bernilai 1) untuk memungkinkan Anda melihat struktur perhitungan ANP tanpa harus mengisi semua pairwise comparison.</p>
                                <p><strong>Untuk hasil yang akurat:</strong> Silakan isi pairwise comparison pada halaman <a href="<?= base_url('tpp/anp/pairwise-comparison') ?>">Pairwise Comparison</a> dan gunakan tombol "Auto Fill Semua" untuk mempercepat proses.</p>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Data hasil ANP tidak tersedia.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
