<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'validasi';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('kalapas/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('kalapas/validasi') ?>">Validasi Hasil</a></li>
    <li class="breadcrumb-item active">Preview Cetak Laporan</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Preview Cetak Laporan Ranking Narapidana</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <form method="get" action="<?= base_url('kalapas/preview-cetak') ?>" class="form-inline">
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
                            <a href="<?= base_url('kalapas/validasi') ?>" class="btn btn-primary mt-2">
                                <i class="fas fa-arrow-left"></i> Kembali ke Validasi
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i> Menampilkan preview laporan ranking untuk periode <strong><?= $periode ?></strong>.
                            Total narapidana: <strong><?= count($narapidana) ?></strong>, Total kriteria: <strong><?= count($kriteria) ?></strong>.
                        </div>
                        
                        <!-- Statistik Ranking -->
                        <div class="row mb-4">
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
                                        <?php 
                                        $totalPreferensi = 0;
                                        foreach ($ranking as $item) {
                                            $totalPreferensi += $item['preferensi'];
                                        }
                                        $rataRata = count($ranking) > 0 ? $totalPreferensi / count($ranking) : 0;
                                        ?>
                                        <span class="info-box-number"><?= number_format($rataRata, 4) ?></span>
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
                        
                        <!-- Tabel Preview -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">Rank</th>
                                        <th width="20%">Nama Narapidana</th>
                                        <th width="15%">Nomor Registrasi</th>
                                        <th width="15%">Kasus</th>
                                        <th width="10%">Jarak Positif (D+)</th>
                                        <th width="10%">Jarak Negatif (D-)</th>
                                        <th width="10%">Nilai Preferensi</th>
                                        <th width="15%">Status Remisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ranking as $index => $item): 
                                        // Tentukan status remisi berdasarkan nilai preferensi
                                        $statusRemisi = '';
                                        $badgeClass = '';
                                        if ($item['preferensi'] >= 0.85) {
                                            $statusRemisi = 'Remisi Penuh';
                                            $badgeClass = 'badge-success';
                                        } elseif ($item['preferensi'] >= 0.75) {
                                            $statusRemisi = 'Remisi Separuh';
                                            $badgeClass = 'badge-warning';
                                        } else {
                                            $statusRemisi = 'Tidak Layak Remisi';
                                            $badgeClass = 'badge-danger';
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td><?= $item['narapidana']['nama_lengkap'] ?></td>
                                        <td><?= $item['narapidana']['nomor_registrasi'] ?></td>
                                        <td><?= $item['narapidana']['kasus'] ?? $item['narapidana']['jenis_kasus'] ?? $item['narapidana']['jenis_kejahatan'] ?? '-' ?></td>
                                        <td class="text-center"><?= number_format($item['d_positif'], 4) ?></td>
                                        <td class="text-center"><?= number_format($item['d_negatif'], 4) ?></td>
                                        <td class="text-center">
                                            <?php if ($item['preferensi'] >= 0.7): ?>
                                                <span class="badge badge-success"><?= number_format($item['preferensi'], 4) ?></span>
                                            <?php elseif ($item['preferensi'] >= 0.5): ?>
                                                <span class="badge badge-warning"><?= number_format($item['preferensi'], 4) ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-danger"><?= number_format($item['preferensi'], 4) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $badgeClass ?>"><?= $statusRemisi ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Tombol Aksi -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <a href="<?= base_url('kalapas/validasi') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Validasi
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?= base_url('kalapas/cetak-laporan?periode=' . $periode) ?>" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-print"></i> Cetak Laporan
                                </a>
                                <a href="<?= base_url('kalapas/cetak-laporan?periode=' . $periode . '&download=1') ?>" class="btn btn-success">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                            </div>
                        </div>
                        
                        <!-- Catatan -->
                        <div class="alert alert-warning mt-3">
                            <h5><i class="icon fas fa-info-circle"></i> Catatan</h5>
                            <p>Pastikan data sudah benar sebelum mencetak. Laporan yang telah dicetak akan disimpan sebagai dokumentasi resmi.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
