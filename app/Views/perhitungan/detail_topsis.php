<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'topsis';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/topsis') ?>">Perhitungan TOPSIS</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/topsis/riwayat') ?>">Riwayat Hasil</a></li>
    <li class="breadcrumb-item active">Detail Perhitungan</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Perhitungan TOPSIS</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('bimkesmaswat/topsis/riwayat') ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
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
                                            <div class="info-box bg-light">
                                                <span class="info-box-icon bg-primary"><i class="fas fa-user"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Nama Narapidana</span>
                                                    <span class="info-box-number"><?= $ranking['nama_lengkap'] ?? 'N/A' ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box bg-light">
                                                <span class="info-box-icon bg-info"><i class="fas fa-id-card"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Nomor Registrasi</span>
                                                    <span class="info-box-number"><?= $ranking['nomor_registrasi'] ?? 'N/A' ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box bg-light">
                                                <span class="info-box-icon bg-success"><i class="fas fa-trophy"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Ranking</span>
                                                    <span class="info-box-number">
                                                        <span class="badge badge-<?= $ranking['ranking'] == 1 ? 'success' : ($ranking['ranking'] <= 3 ? 'warning' : 'secondary') ?>">
                                                            <?= $ranking['ranking'] ?>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box bg-light">
                                                <span class="info-box-icon bg-<?= $ranking['status'] == 'Remisi Penuh' ? 'success' : ($ranking['status'] == 'Remisi Separuh' ? 'warning' : 'danger') ?>">
                                                    <i class="fas fa-<?= $ranking['status'] == 'Remisi Penuh' ? 'check-circle' : ($ranking['status'] == 'Remisi Separuh' ? 'exclamation-circle' : 'times-circle') ?>"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Status Remisi</span>
                                                    <span class="info-box-number">
                                                        <span class="badge badge-<?= $ranking['status'] == 'Remisi Penuh' ? 'success' : ($ranking['status'] == 'Remisi Separuh' ? 'warning' : 'danger') ?>">
                                                            <?= $ranking['status'] ?>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail Perhitungan -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Detail Perhitungan TOPSIS</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($detail) && !empty($detail)): ?>
                                        <!-- Nilai Preferensi -->
                                        <div class="mb-4">
                                            <h5>1. Nilai Preferensi (Ci)</h5>
                                            <div class="alert alert-info">
                                                <h6><i class="fas fa-calculator"></i> Rumus Perhitungan</h6>
                                                <p>Ci = D- / (D+ + D-)</p>
                                                <p>Dimana:</p>
                                                <ul>
                                                    <li>D+ = Jarak ke solusi ideal positif = <?= number_format($detail['jarak_positif'] ?? 0, 4) ?></li>
                                                    <li>D- = Jarak ke solusi ideal negatif = <?= number_format($detail['jarak_negatif'] ?? 0, 4) ?></li>
                                                </ul>
                                                <p><strong>Hasil:</strong> Ci = <?= number_format($detail['jarak_negatif'] ?? 0, 4) ?> / (<?= number_format($detail['jarak_positif'] ?? 0, 4) ?> + <?= number_format($detail['jarak_negatif'] ?? 0, 4) ?>) = <?= number_format($ranking['nilai_preferensi'] ?? 0, 4) ?></p>
                                            </div>
                                        </div>
                                        
                                        <!-- Nilai Kriteria -->
                                        <div class="mb-4">
                                            <h5>2. Nilai Kriteria</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Kode</th>
                                                            <th>Nama Kriteria</th>
                                                            <th>Jenis</th>
                                                            <th>Nilai Asli (0-100)</th>
                                                            <th>Skala Konversi</th>
                                                            <th>Bobot ANP</th>
                                                            <th>Nilai Terbobot</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (isset($detail['kriteria']) && is_array($detail['kriteria'])): ?>
                                                        <?php foreach ($detail['kriteria'] as $kriteria): ?>
                                                        <tr>
                                                            <td><?= $kriteria['kode'] ?? 'N/A' ?></td>
                                                            <td><?= $kriteria['nama'] ?? 'N/A' ?></td>
                                                            <td>
                                                                <span class="badge badge-<?= ($kriteria['jenis'] ?? 'Benefit') == 'Benefit' ? 'success' : 'danger' ?>">
                                                                    <?= $kriteria['jenis'] ?? 'Benefit' ?>
                                                                </span>
                                                            </td>
                                                            <td><?= number_format($kriteria['nilai_asli'] ?? 0, 2) ?></td>
                                                            <td><?= $kriteria['skala_konversi'] ?? 'N/A' ?></td>
                                                            <td><?= number_format($kriteria['bobot_anp'] ?? 0, 4) ?></td>
                                                            <td><?= number_format($kriteria['nilai_terbobot'] ?? 0, 4) ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                        <?php else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center">Data kriteria tidak tersedia</td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <!-- Jarak ke Solusi Ideal -->
                                        <div class="mb-4">
                                            <h5>3. Jarak ke Solusi Ideal</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-header bg-success">
                                                            <h6 class="card-title">Jarak ke Solusi Ideal Positif (D+)</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <p>D+ = √[Σ(nilai_terbobot - solusi_ideal_positif)²]</p>
                                                            <p><strong>Hasil:</strong> <?= number_format($detail['jarak_positif'] ?? 0, 4) ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-header bg-danger">
                                                            <h6 class="card-title">Jarak ke Solusi Ideal Negatif (D-)</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <p>D- = √[Σ(nilai_terbobot - solusi_ideal_negatif)²]</p>
                                                            <p><strong>Hasil:</strong> <?= number_format($detail['jarak_negatif'] ?? 0, 4) ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Kriteria Status -->
                                        <div class="mb-4">
                                            <h5>4. Kriteria Status Remisi</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Status</th>
                                                            <th>Nilai Preferensi (Ci)</th>
                                                            <th>Keterangan</th>
                                                            <th>Status Saat Ini</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="<?= $ranking['nilai_preferensi'] >= 0.85 ? 'table-success' : '' ?>">
                                                            <td><strong>Remisi Penuh</strong></td>
                                                            <td>Ci ≥ 0.8500</td>
                                                            <td>Narapidana terbaik</td>
                                                            <td>
                                                                <?php if ($ranking['nilai_preferensi'] >= 0.85): ?>
                                                                <span class="badge badge-success">✓ Sesuai</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr class="<?= $ranking['nilai_preferensi'] >= 0.75 && $ranking['nilai_preferensi'] < 0.85 ? 'table-warning' : '' ?>">
                                                            <td><strong>Remisi Separuh</strong></td>
                                                            <td>0.7500 ≤ Ci < 0.8500</td>
                                                            <td>Narapidana rata-rata</td>
                                                            <td>
                                                                <?php if ($ranking['nilai_preferensi'] >= 0.75 && $ranking['nilai_preferensi'] < 0.85): ?>
                                                                <span class="badge badge-warning">✓ Sesuai</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr class="<?= $ranking['nilai_preferensi'] < 0.75 ? 'table-danger' : '' ?>">
                                                            <td><strong>Tidak Layak</strong></td>
                                                            <td>Ci < 0.7500</td>
                                                            <td>Perlu perhatian khusus</td>
                                                            <td>
                                                                <?php if ($ranking['nilai_preferensi'] < 0.75): ?>
                                                                <span class="badge badge-danger">✓ Sesuai</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <!-- Kesimpulan -->
                                        <div class="mb-4">
                                            <h5>5. Kesimpulan</h5>
                                            <div class="alert alert-<?= $ranking['status'] == 'Remisi Penuh' ? 'success' : ($ranking['status'] == 'Remisi Separuh' ? 'warning' : 'danger') ?>">
                                                <h6><i class="fas fa-<?= $ranking['status'] == 'Remisi Penuh' ? 'check-circle' : ($ranking['status'] == 'Remisi Separuh' ? 'exclamation-circle' : 'times-circle') ?>"></i> Status Akhir</h6>
                                                <p>Berdasarkan perhitungan TOPSIS, narapidana <strong><?= $ranking['nama_lengkap'] ?? 'N/A' ?></strong> dengan nomor registrasi <strong><?= $ranking['nomor_registrasi'] ?? 'N/A' ?></strong> memiliki:</p>
                                                <ul>
                                                    <li>Nilai Preferensi (Ci): <strong><?= number_format($ranking['nilai_preferensi'] ?? 0, 4) ?></strong></li>
                                                    <li>Ranking: <strong>#<?= $ranking['ranking'] ?></strong></li>
                                                    <li>Status Remisi: <strong><?= $ranking['status'] ?></strong></li>
                                                </ul>
                                                <p><strong>Keterangan:</strong> <?= $ranking['status'] == 'Remisi Penuh' ? 'Narapidana terbaik dan layak mendapatkan remisi penuh.' : ($ranking['status'] == 'Remisi Separuh' ? 'Narapidana rata-rata dan layak mendapatkan remisi separuh.' : 'Narapidana perlu perhatian khusus dan tidak layak mendapatkan remisi.') ?></p>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <h5><i class="icon fas fa-exclamation-triangle"></i> Informasi</h5>
                                            <p>Detail perhitungan tidak tersedia.</p>
                                            <p>Data detail perhitungan mungkin belum disimpan atau terjadi kesalahan dalam pengambilan data.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="<?= base_url('bimkesmaswat/topsis/riwayat') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
                            </a>
                            <a href="<?= base_url('bimkesmaswat/topsis') ?>" class="btn btn-primary">
                                <i class="fas fa-calculator"></i> Hitung TOPSIS Baru
                            </a>
                            <a href="<?= base_url('bimkesmaswat/dashboard') ?>" class="btn btn-info">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        // Auto-collapse detail perhitungan jika terlalu panjang
        $('.card-body .collapse').on('shown.bs.collapse', function() {
            $(this).parent().find('.fa-minus').removeClass('fa-minus').addClass('fa-plus');
        }).on('hidden.bs.collapse', function() {
            $(this).parent().find('.fa-plus').removeClass('fa-plus').addClass('fa-minus');
        });
    });
    </script>
<?= $this->endSection() ?>