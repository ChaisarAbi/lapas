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
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/bobot') ?>">Input Bobot</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/bobot/matriks') ?>">Matriks Perbandingan</a></li>
    <li class="breadcrumb-item active">Validasi Konsistensi</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Validasi Konsistensi Matriks Perbandingan</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('tpp/anp') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-chart-bar"></i> Lihat Hasil ANP
                        </a>
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
                    
                    <?php if ($konsistensi['konsisten']): ?>
                        <div class="alert alert-success">
                            <h4><i class="icon fas fa-check"></i> Matriks Konsisten!</h4>
                            <p>Consistency Ratio (CR) = <?= number_format($konsistensi['cr'], 4) ?> ≤ 0.1</p>
                            <p>Matriks perbandingan berpasangan sudah konsisten dan dapat digunakan untuk perhitungan ANP.</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <h4><i class="icon fas fa-exclamation-triangle"></i> Matriks Tidak Konsisten!</h4>
                            <p>Consistency Ratio (CR) = <?= number_format($konsistensi['cr'], 4) ?> > 0.1</p>
                            <p>Matriks perbandingan berpasangan tidak konsisten. Silakan perbaiki nilai perbandingan.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Hasil Perhitungan Konsistensi</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="60%">Jumlah Kriteria (n)</td>
                                            <td class="text-right"><?= $konsistensi['n'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>λ Maksimum (λmax)</td>
                                            <td class="text-right"><?= number_format($konsistensi['lambda_max'], 4) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Consistency Index (CI)</td>
                                            <td class="text-right"><?= number_format($konsistensi['ci'], 4) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Random Index (RI)</td>
                                            <td class="text-right"><?= number_format($konsistensi['ri'], 4) ?></td>
                                        </tr>
                                        <tr class="<?= $konsistensi['konsisten'] ? 'table-success' : 'table-danger' ?>">
                                            <td><strong>Consistency Ratio (CR)</strong></td>
                                            <td class="text-right"><strong><?= number_format($konsistensi['cr'], 4) ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status Konsistensi</strong></td>
                                            <td class="text-right">
                                                <span class="badge badge-<?= $konsistensi['konsisten'] ? 'success' : 'danger' ?>">
                                                    <?= $konsistensi['konsisten'] ? 'KONSISTEN' : 'TIDAK KONSISTEN' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <div class="alert alert-info">
                                        <h6><i class="icon fas fa-info-circle"></i> Kriteria Konsistensi</h6>
                                        <p>Matriks dikatakan konsisten jika <strong>CR ≤ 0.1</strong> (10%).</p>
                                        <p>CR = CI / RI</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bobot Eigen (Prioritas)</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Kriteria</th>
                                                    <th>Bobot Eigen</th>
                                                    <th>Persentase</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($kriteria as $index => $k): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-info"><?= $k['kode'] ?></span><br>
                                                        <small><?= $k['nama'] ?></small>
                                                    </td>
                                                    <td class="text-right"><?= number_format($konsistensi['bobot_eigen'][$index], 4) ?></td>
                                                    <td>
                                                        <div class="progress progress-xs">
                                                            <div class="progress-bar bg-primary" 
                                                                 style="width: <?= $konsistensi['bobot_eigen'][$index] * 100 ?>%"></div>
                                                        </div>
                                                        <small><?= number_format($konsistensi['bobot_eigen'][$index] * 100, 1) ?>%</small>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Matriks Normalisasi</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th width="15%">Kriteria</th>
                                                    <?php foreach ($kriteria as $k): ?>
                                                    <th width="<?= 85 / count($kriteria) ?>%" class="text-center">
                                                        <span class="badge badge-info"><?= $k['kode'] ?></span>
                                                    </th>
                                                    <?php endforeach; ?>
                                                    <th width="10%" class="text-center">Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($kriteria as $i => $kriteriaI): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= $kriteriaI['kode'] ?></strong>
                                                    </td>
                                                    <?php foreach ($kriteria as $j => $kriteriaJ): ?>
                                                    <td class="text-right">
                                                        <?= number_format($konsistensi['matriks_normalisasi'][$i][$j], 4) ?>
                                                    </td>
                                                    <?php endforeach; ?>
                                                    <td class="text-right bg-light">
                                                        <strong><?= number_format(array_sum($konsistensi['matriks_normalisasi'][$i]), 4) ?></strong>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <tr class="bg-light">
                                                    <td><strong>Jumlah Kolom</strong></td>
                                                    <?php 
                                                    $jumlahKolom = [];
                                                    for ($j = 0; $j < count($kriteria); $j++) {
                                                        $jumlah = 0;
                                                        for ($i = 0; $i < count($kriteria); $i++) {
                                                            $jumlah += $konsistensi['matriks_normalisasi'][$i][$j];
                                                        }
                                                        $jumlahKolom[$j] = $jumlah;
                                                    }
                                                    ?>
                                                    <?php foreach ($jumlahKolom as $jumlah): ?>
                                                    <td class="text-right">
                                                        <strong><?= number_format($jumlah, 4) ?></strong>
                                                    </td>
                                                    <?php endforeach; ?>
                                                    <td class="text-right">
                                                        <strong><?= number_format(array_sum($jumlahKolom), 4) ?></strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <a href="<?= base_url('tpp/bobot/matriks') ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-edit"></i> Perbaiki Matriks
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= base_url('tpp/anp') ?>" class="btn btn-success btn-block">
                                <i class="fas fa-arrow-right"></i> Lanjut ke Hasil ANP
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
