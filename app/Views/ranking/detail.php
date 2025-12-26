<?= $this->extend('layouts/dashboard_template') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url($dashboard_url) ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('ranking') ?>">Ranking Narapidana</a></li>
    <li class="breadcrumb-item active">Detail Penilaian</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Penilaian Narapidana</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('ranking') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke Ranking
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-ban"></i> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Informasi Narapidana -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Narapidana</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Nama Lengkap:</strong><br>
                                            <?= $narapidana['nama_lengkap'] ?? 'Tidak tersedia' ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>NIK:</strong><br>
                                            <?= $narapidana['nik'] ?? 'Tidak tersedia' ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>No. Register:</strong><br>
                                            <?= $narapidana['no_register'] ?? 'Tidak tersedia' ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Periode:</strong><br>
                                            <?= $periode ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail Penilaian -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Detail Nilai per Kriteria</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($penilaian)): ?>
                                        <div class="alert alert-warning">
                                            <i class="icon fas fa-exclamation-triangle"></i>
                                            Tidak ada data penilaian untuk narapidana ini pada periode <?= $periode ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th width="15%">Kode Kriteria</th>
                                                        <th width="30%">Nama Kriteria</th>
                                                        <th width="15%">Jenis</th>
                                                        <th width="15%">Nilai</th>
                                                        <th width="20%">Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $no = 1; ?>
                                                    <?php foreach ($penilaian as $p): ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no++ ?></td>
                                                        <td>
                                                            <span class="badge badge-info"><?= $p['kriteria_kode'] ?></span>
                                                        </td>
                                                        <td><?= $p['kriteria_nama'] ?></td>
                                                        <td>
                                                            <span class="badge badge-<?= $p['kriteria_jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                                                                <?= $p['kriteria_jenis'] ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-primary" style="font-size: 1.1em;">
                                                                <?= number_format($p['nilai'], 2) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            // Tentukan keterangan berdasarkan nilai
                                                            if ($p['nilai'] >= 4.5) {
                                                                echo '<span class="badge badge-success">Sangat Baik</span>';
                                                            } elseif ($p['nilai'] >= 3.5) {
                                                                echo '<span class="badge badge-info">Baik</span>';
                                                            } elseif ($p['nilai'] >= 2.5) {
                                                                echo '<span class="badge badge-warning">Cukup</span>';
                                                            } elseif ($p['nilai'] >= 1.5) {
                                                                echo '<span class="badge badge-secondary">Kurang</span>';
                                                            } else {
                                                                echo '<span class="badge badge-danger">Sangat Kurang</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informasi Tambahan -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi</h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Keterangan Skala Nilai:</strong></p>
                                    <ul>
                                        <li><span class="badge badge-success">Sangat Baik</span>: 4.5 - 5.0</li>
                                        <li><span class="badge badge-info">Baik</span>: 3.5 - 4.4</li>
                                        <li><span class="badge badge-warning">Cukup</span>: 2.5 - 3.4</li>
                                        <li><span class="badge badge-secondary">Kurang</span>: 1.5 - 2.4</li>
                                        <li><span class="badge badge-danger">Sangat Kurang</span>: 0.0 - 1.4</li>
                                    </ul>
                                    <p class="text-muted"><small>Data ini digunakan untuk perhitungan ranking menggunakan metode TOPSIS.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
