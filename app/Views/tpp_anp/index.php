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
        <a href="<?= base_url('tpp/kriteria') ?>" class="nav-link">
            <i class="nav-icon fas fa-balance-scale"></i>
            <p>Kelola Kriteria</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/bobot') ?>" class="nav-link">
            <i class="nav-icon fas fa-calculator"></i>
            <p>Input Bobot</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/bobot/matriks') ?>" class="nav-link">
            <i class="nav-icon fas fa-check-circle"></i>
            <p>Validasi Konsistensi</p>
        </a>
    </li>
    <li class="nav-header">LAPORAN</li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/anp') ?>" class="nav-link active">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Hasil ANP</p>
        </a>
    </li>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Hasil Analytic Network Process (ANP)</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hasil Analytic Network Process (ANP)</h3>
                   
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
                    
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info-circle"></i> Tentang Analytic Network Process (ANP)</h5>
                        <p>ANP adalah metode pengambilan keputusan yang memperhitungkan interdependensi antar kriteria. Hasil ANP berupa bobot akhir yang digunakan dalam perhitungan TOPSIS.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Ringkasan Hasil ANP</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="60%">Jumlah Kriteria</td>
                                            <td class="text-right"><?= $hasilAnp['n'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>λ Maksimum (λmax)</td>
                                            <td class="text-right"><?= number_format($hasilAnp['lambda_max'], 4) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Consistency Index (CI)</td>
                                            <td class="text-right"><?= number_format($hasilAnp['ci'], 4) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Random Index (RI)</td>
                                            <td class="text-right"><?= number_format($hasilAnp['ri'], 4) ?></td>
                                        </tr>
                                        <tr class="<?= $hasilAnp['konsisten'] ? 'table-success' : 'table-danger' ?>">
                                            <td><strong>Consistency Ratio (CR)</strong></td>
                                            <td class="text-right"><strong><?= number_format($hasilAnp['cr'], 4) ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status Konsistensi</strong></td>
                                            <td class="text-right">
                                                <span class="badge badge-<?= $hasilAnp['konsisten'] ? 'success' : 'danger' ?>">
                                                    <?= $hasilAnp['konsisten'] ? 'KONSISTEN' : 'TIDAK KONSISTEN' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Aksi</h3>
                                </div>
                                <div class="card-body">
                                    <form action="<?= base_url('tpp/anp/simpan-bobot') ?>" method="post">
                                        <?= csrf_field() ?>
                                        
                                        <?php foreach ($kriteria as $index => $k): ?>
                                            <input type="hidden" name="kriteria_id[]" value="<?= $k['id'] ?>">
                                            <input type="hidden" name="bobot_akhir[]" value="<?= $hasilAnp['bobot_akhir'][$index] ?>">
                                        <?php endforeach; ?>
                                        
                                        <button type="submit" class="btn btn-primary btn-block mb-2" 
                                                <?= !$hasilAnp['konsisten'] ? 'disabled' : '' ?>>
                                            <i class="fas fa-save"></i> Simpan Bobot Akhir ke Database
                                        </button>
                                        
                                        <?php if (!$hasilAnp['konsisten']): ?>
                                            <div class="alert alert-warning">
                                                <i class="icon fas fa-exclamation-triangle"></i> 
                                                Tidak dapat menyimpan karena matriks tidak konsisten (CR > 0.1)
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                    
                                    <a href="<?= base_url('tpp/bobot/konsistensi') ?>" class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Validasi Konsistensi
                                    </a>
                                    
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bobot Akhir Kriteria</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="5%">Rank</th>
                                                    <th width="10%">Kode</th>
                                                    <th width="35%">Nama Kriteria</th>
                                                    <th width="15%">Jenis</th>
                                                    <th width="15%">Bobot Prioritas</th>
                                                    <th width="15%">Bobot Akhir</th>
                                                    <th width="5%">Persentase</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                // Gabungkan data kriteria dengan bobot akhir untuk sorting
                                                $kriteriaBobot = [];
                                                foreach ($kriteria as $index => $k) {
                                                    $kriteriaBobot[] = [
                                                        'kriteria' => $k,
                                                        'bobot_akhir' => $hasilAnp['bobot_akhir'][$index],
                                                        'bobot_prioritas' => $hasilAnp['bobot_prioritas'][$index]
                                                    ];
                                                }
                                                
                                                // Urutkan berdasarkan bobot akhir (descending)
                                                usort($kriteriaBobot, function($a, $b) {
                                                    return $b['bobot_akhir'] <=> $a['bobot_akhir'];
                                                });
                                                ?>
                                                
                                                <?php foreach ($kriteriaBobot as $rank => $item): ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <span class="badge badge-<?= 
                                                            $rank == 0 ? 'success' : 
                                                            ($rank == 1 ? 'info' : 
                                                            ($rank == 2 ? 'warning' : 'secondary'))
                                                        ?>">
                                                            <?= $rank + 1 ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info"><?= $item['kriteria']['kode'] ?></span>
                                                    </td>
                                                    <td><?= $item['kriteria']['nama'] ?></td>
                                                    <td>
                                                        <span class="badge badge-<?= $item['kriteria']['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                                                            <?= $item['kriteria']['jenis'] ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-right"><?= number_format($item['bobot_prioritas'], 4) ?></td>
                                                    <td class="text-right">
                                                        <span class="badge badge-<?= 
                                                            $item['bobot_akhir'] >= 0.2 ? 'primary' : 
                                                            ($item['bobot_akhir'] >= 0.1 ? 'info' : 'secondary')
                                                        ?>">
                                                            <?= number_format($item['bobot_akhir'], 4) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="progress progress-xs">
                                                            <div class="progress-bar bg-primary" 
                                                                 style="width: <?= $item['bobot_akhir'] * 100 ?>%"></div>
                                                        </div>
                                                        <small><?= number_format($item['bobot_akhir'] * 100, 1) ?>%</small>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                                    <td class="text-right">
                                                        <strong><?= number_format(array_sum($hasilAnp['bobot_prioritas']), 4) ?></strong>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong><?= number_format(array_sum($hasilAnp['bobot_akhir']), 4) ?></strong>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong>100%</strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Distribusi Bobot Berdasarkan Jenis</h3>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $totalBenefit = 0;
                                    $totalCost = 0;
                                    
                                    foreach ($kriteriaBobot as $item) {
                                        if ($item['kriteria']['jenis'] == 'Benefit') {
                                            $totalBenefit += $item['bobot_akhir'];
                                        } else {
                                            $totalCost += $item['bobot_akhir'];
                                        }
                                    }
                                    ?>
                                    
                                    <div class="progress-group">
                                        <span class="progress-text">Kriteria Benefit</span>
                                        <span class="float-right">
                                            <b><?= number_format($totalBenefit, 4) ?></b> / <?= number_format($totalBenefit * 100, 1) ?>%
                                        </span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-success" style="width: <?= $totalBenefit * 100 ?>%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="progress-group">
                                        <span class="progress-text">Kriteria Cost</span>
                                        <span class="float-right">
                                            <b><?= number_format($totalCost, 4) ?></b> / <?= number_format($totalCost * 100, 1) ?>%
                                        </span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-danger" style="width: <?= $totalCost * 100 ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Interpretasi Hasil</h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Kriteria dengan bobot tertinggi</strong> adalah yang paling berpengaruh dalam penilaian narapidana.</p>
                                    <p><strong>Kriteria benefit</strong> (warna hijau) semakin tinggi nilai semakin baik.</p>
                                    <p><strong>Kriteria cost</strong> (warna merah) semakin rendah nilai semakin baik.</p>
                                    <p class="text-muted"><small>Bobot ini akan digunakan dalam perhitungan TOPSIS untuk ranking narapidana.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
