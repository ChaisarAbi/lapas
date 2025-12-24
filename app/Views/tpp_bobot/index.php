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
        <a href="<?= base_url('tpp/bobot') ?>" class="nav-link active">
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
        <a href="<?= base_url('tpp/anp') ?>" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Hasil ANP</p>
        </a>
    </li>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Input Bobot Kriteria</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Input Bobot Kriteria</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('tpp/bobot/matriks') ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-table"></i> Matriks Perbandingan
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
                    
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info-circle"></i> Petunjuk Input Bobot</h5>
                        <p>Masukkan bobot untuk setiap kriteria. Total bobot semua kriteria harus sama dengan <strong>1.000</strong>.</p>
                        <p>Bobot menentukan tingkat kepentingan relatif antar kriteria dalam perhitungan ANP.</p>
                    </div>
                    
                    <form action="<?= base_url('tpp/bobot/simpan') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Nama Kriteria</th>
                                        <th width="15%">Jenis</th>
                                        <th width="15%">Bobot Saat Ini</th>
                                        <th width="10%">Bobot Baru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($kriteria)): ?>
                                        <?php foreach ($kriteria as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <span class="badge badge-info"><?= $item['kode'] ?></span>
                                            </td>
                                            <td><?= $item['nama'] ?></td>
                                            <td>
                                                <span class="badge badge-<?= $item['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                                                    <?= $item['jenis'] ?>
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <span class="badge badge-<?= 
                                                    $item['bobot'] >= 0.2 ? 'primary' : 
                                                    ($item['bobot'] >= 0.1 ? 'info' : 'secondary')
                                                ?>">
                                                    <?= number_format($item['bobot'], 3) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <input type="hidden" name="kriteria_id[]" value="<?= $item['id'] ?>">
                                                <input type="number" step="0.001" min="0" max="1" 
                                                       class="form-control form-control-sm" 
                                                       name="bobot[]" value="<?= $item['bobot'] ?>" 
                                                       required>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data kriteria</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total Bobot:</strong></td>
                                        <td class="text-right">
                                            <span class="badge badge-<?= 
                                                $totalBobot == 1 ? 'success' : 
                                                ($totalBobot > 1 ? 'danger' : 'warning')
                                            ?>">
                                                <?= number_format($totalBobot, 3) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-block btn-sm">
                                                <i class="fas fa-save"></i> Simpan Bobot
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Status Bobot</h3>
                                </div>
                                <div class="card-body">
                                    <?php if ($totalBobot == 1): ?>
                                        <div class="alert alert-success">
                                            <h4><i class="icon fas fa-check"></i> Bobot Valid</h4>
                                            <p>Total bobot = 1.000 (sudah sesuai untuk perhitungan ANP)</p>
                                        </div>
                                    <?php elseif ($totalBobot > 1): ?>
                                        <div class="alert alert-danger">
                                            <h4><i class="icon fas fa-exclamation-triangle"></i> Bobot Tidak Valid</h4>
                                            <p>Total bobot = <?= number_format($totalBobot, 3) ?> (melebihi 1.000)</p>
                                            <p>Silakan kurangi bobot beberapa kriteria.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <h4><i class="icon fas fa-exclamation-circle"></i> Bobot Kurang</h4>
                                            <p>Total bobot = <?= number_format($totalBobot, 3) ?> (kurang dari 1.000)</p>
                                            <p>Silakan tambah bobot beberapa kriteria.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Distribusi Bobot</h3>
                                </div>
                                <div class="card-body">
                                    <div class="progress-group">
                                        <span class="progress-text">Kriteria Benefit</span>
                                        <span class="float-right">
                                            <?php
                                            $totalBenefit = 0;
                                            foreach ($kriteria as $item) {
                                                if ($item['jenis'] == 'Benefit') {
                                                    $totalBenefit += $item['bobot'];
                                                }
                                            }
                                            ?>
                                            <b><?= number_format($totalBenefit, 3) ?></b>
                                        </span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-success" style="width: <?= $totalBenefit * 100 ?>%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="progress-group">
                                        <span class="progress-text">Kriteria Cost</span>
                                        <span class="float-right">
                                            <?php
                                            $totalCost = 0;
                                            foreach ($kriteria as $item) {
                                                if ($item['jenis'] == 'Cost') {
                                                    $totalCost += $item['bobot'];
                                                }
                                            }
                                            ?>
                                            <b><?= number_format($totalCost, 3) ?></b>
                                        </span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-danger" style="width: <?= $totalCost * 100 ?>%"></div>
                                        </div>
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
