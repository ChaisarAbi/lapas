<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'validasi';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('kalapas/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Validasi Hasil</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Validasi Hasil Penilaian Narapidana</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <form method="get" action="<?= base_url('kalapas/validasi') ?>" class="form-inline">
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
                    <?php if (empty($ranking)): ?>
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Data Tidak Tersedia</h5>
                            <p>Tidak ada data ranking untuk periode <strong><?= $periode ?></strong>. Pastikan:</p>
                            <ol>
                                <li>Sudah ada input penilaian dari petugas BIMKEMASWAT</li>
                                <li>Periode yang dipilih sesuai dengan periode penilaian</li>
                                <li>Data kriteria dan bobot sudah diatur oleh TPP</li>
                            </ol>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i> Menampilkan data untuk periode <strong><?= $periode ?></strong>
                        </div>
                        
                        <form method="post" action="<?= base_url('kalapas/validasi/simpan') ?>">
                            <input type="hidden" name="periode" value="<?= $periode ?>">
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">Rank</th>
                                            <th width="20%">Narapidana</th>
                                            <th width="15%">Nomor Registrasi</th>
                                            <th width="15%">Jenis Kejahatan</th>
                                            <th width="15%">Nilai Preferensi</th>
                                            <th width="15%">Status</th>
                                            <th width="15%">Validasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ranking as $index => $item): ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-<?= $index < 3 ? 'success' : ($index < 10 ? 'warning' : 'secondary') ?>">
                                                    <?= $index + 1 ?>
                                                </span>
                                            </td>
                                            <td><?= $item['narapidana']['nama_lengkap'] ?></td>
                                            <td><?= $item['narapidana']['nomor_registrasi'] ?></td>
                                            <td><?= $item['narapidana']['jenis_kejahatan'] ?? '-' ?></td>
                                            <td>
                                                <span class="badge badge-<?= $item['preferensi'] >= 0.7 ? 'success' : ($item['preferensi'] >= 0.5 ? 'warning' : 'danger') ?>">
                                                    <?= number_format($item['preferensi'], 4) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($item['preferensi'] >= 0.7): ?>
                                                    <span class="badge badge-success">Baik</span>
                                                <?php elseif ($item['preferensi'] >= 0.5): ?>
                                                    <span class="badge badge-warning">Cukup</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Perlu Perhatian</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <select name="validasi[<?= $item['narapidana']['id'] ?>]" class="form-control form-control-sm">
                                                    <option value="menunggu">Menunggu</option>
                                                    <option value="disetujui" selected>Disetujui</option>
                                                    <option value="perlu_review">Perlu Review</option>
                                                    <option value="ditolak">Ditolak</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Statistik Validasi</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3 col-sm-6 col-12">
                                                    <div class="info-box bg-info">
                                                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Total Narapidana</span>
                                                            <span class="info-box-number"><?= count($ranking) ?></span>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: 100%"></div>
                                                            </div>
                                                            <span class="progress-description">
                                                                Data ranking
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
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
                                                            <span class="info-box-text">Rata-rata Nilai</span>
                                                            <?php 
                                                            $totalPreferensi = 0;
                                                            foreach ($ranking as $item) {
                                                                $totalPreferensi += $item['preferensi'];
                                                            }
                                                            $rataPreferensi = count($ranking) > 0 ? $totalPreferensi / count($ranking) : 0;
                                                            ?>
                                                            <span class="info-box-number"><?= number_format($rataPreferensi, 4) ?></span>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: <?= $rataPreferensi * 100 ?>%"></div>
                                                            </div>
                                                            <span class="progress-description">
                                                                Skala 0-1
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
                            
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Catatan Validasi</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="catatan">Catatan (Opsional)</label>
                                                <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Masukkan catatan validasi jika diperlukan..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check-circle"></i> Simpan Validasi
                                    </button>
                                    <a href="<?= base_url('kalapas/ranking/cetak?periode=' . $periode) ?>" target="_blank" class="btn btn-primary btn-lg">
                                        <i class="fas fa-print"></i> Cetak Laporan
                                    </a>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i> Validasi ini akan menjadi keputusan final untuk periode <?= $periode ?>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
