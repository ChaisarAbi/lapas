<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'hasil-validasi';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('kalapas/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Hasil Validasi</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hasil Validasi Penilaian Narapidana</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <form method="get" action="<?= base_url('kalapas/hasil-validasi') ?>" class="form-inline">
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
                    <?php if (empty($validasi)): ?>
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Data Tidak Tersedia</h5>
                            <p>Tidak ada data validasi untuk periode <strong><?= $periode ?></strong>. Pastikan:</p>
                            <ol>
                                <li>Sudah melakukan validasi di halaman Validasi Hasil</li>
                                <li>Periode yang dipilih sesuai dengan periode validasi</li>
                            </ol>
                            <a href="<?= base_url('kalapas/validasi?periode=' . $periode) ?>" class="btn btn-primary mt-2">
                                <i class="fas fa-check-circle"></i> Lakukan Validasi
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i> Menampilkan data validasi untuk periode <strong><?= $periode ?></strong>
                        </div>
                        
                        <!-- Statistik Validasi -->
                        <div class="row mb-4">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Menunggu</span>
                                        <span class="info-box-number"><?= $statistik['menunggu'] ?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= $statistik['total'] > 0 ? ($statistik['menunggu'] / $statistik['total']) * 100 : 0 ?>%"></div>
                                        </div>
                                        <span class="progress-description">
                                            <?= $statistik['total'] > 0 ? round(($statistik['menunggu'] / $statistik['total']) * 100, 1) : 0 ?>% dari total
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Disetujui</span>
                                        <span class="info-box-number"><?= $statistik['disetujui'] ?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= $statistik['total'] > 0 ? ($statistik['disetujui'] / $statistik['total']) * 100 : 0 ?>%"></div>
                                        </div>
                                        <span class="progress-description">
                                            <?= $statistik['total'] > 0 ? round(($statistik['disetujui'] / $statistik['total']) * 100, 1) : 0 ?>% dari total
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Perlu Review</span>
                                        <span class="info-box-number"><?= $statistik['perlu_review'] ?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= $statistik['total'] > 0 ? ($statistik['perlu_review'] / $statistik['total']) * 100 : 0 ?>%"></div>
                                        </div>
                                        <span class="progress-description">
                                            <?= $statistik['total'] > 0 ? round(($statistik['perlu_review'] / $statistik['total']) * 100, 1) : 0 ?>% dari total
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Ditolak</span>
                                        <span class="info-box-number"><?= $statistik['ditolak'] ?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= $statistik['total'] > 0 ? ($statistik['ditolak'] / $statistik['total']) * 100 : 0 ?>%"></div>
                                        </div>
                                        <span class="progress-description">
                                            <?= $statistik['total'] > 0 ? round(($statistik['ditolak'] / $statistik['total']) * 100, 1) : 0 ?>% dari total
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabel Hasil Validasi -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Narapidana</th>
                                        <th width="15%">Nomor Registrasi</th>
                                        <th width="15%">Status Validasi</th>
                                        <th width="20%">Catatan</th>
                                        <th width="15%">Validator</th>
                                        <th width="10%">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($validasi as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $item['nama_lengkap'] ?></td>
                                        <td><?= $item['nomor_registrasi'] ?></td>
                                        <td>
                                            <?php if ($item['status_validasi'] == 'disetujui'): ?>
                                                <span class="badge badge-success">Disetujui</span>
                                            <?php elseif ($item['status_validasi'] == 'perlu_review'): ?>
                                                <span class="badge badge-warning">Perlu Review</span>
                                            <?php elseif ($item['status_validasi'] == 'ditolak'): ?>
                                                <span class="badge badge-danger">Ditolak</span>
                                            <?php else: ?>
                                                <span class="badge badge-info">Menunggu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $item['catatan'] ?: '-' ?></td>
                                        <td><?= $item['validator_nama'] ?: 'Sistem' ?></td>
                                        <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Ringkasan -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Ringkasan Validasi</h3>
                                    </div>
                                    <div class="card-body">
                                        <p>Total data validasi: <strong><?= $statistik['total'] ?></strong> narapidana</p>
                                        <p>Persentase disetujui: <strong><?= $statistik['total'] > 0 ? round(($statistik['disetujui'] / $statistik['total']) * 100, 1) : 0 ?>%</strong></p>
                                        <p>Data validasi ini digunakan sebagai dasar pengambilan keputusan akhir untuk periode <?= $periode ?>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i> Data validasi disimpan di database tabel "validasi".
                            </p>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="<?= base_url('kalapas/validasi?periode=' . $periode) ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Validasi
                            </a>
                            <a href="<?= base_url('kalapas/ranking/cetak?periode=' . $periode) ?>" target="_blank" class="btn btn-success">
                                <i class="fas fa-print"></i> Cetak Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
