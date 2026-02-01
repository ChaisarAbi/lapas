<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'topsis';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('topsis') ?>">Perhitungan TOPSIS</a></li>
    <li class="breadcrumb-item active">Hasil Perhitungan</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hasil Perhitungan TOPSIS</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('topsis/riwayat?periode_id=' . $periode['id']) ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-history"></i> Riwayat
                        </a>
                        <a href="<?= base_url('topsis/exportPdf/' . $periode['id']) ?>" class="btn btn-success btn-sm" target="_blank">
                            <i class="fas fa-file-pdf"></i> Export PDF
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
                    
                    <!-- Informasi Periode -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-info"><i class="far fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Periode Penilaian</span>
                                    <span class="info-box-number"><?= $periode['nama_periode'] ?> (<?= $periode['tahun'] ?>-<?= str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT) ?>)</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Narapidana</span>
                                    <span class="info-box-number"><?= $total_narapidana ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-warning"><i class="fas fa-list-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Kriteria</span>
                                    <span class="info-box-number"><?= $total_kriteria ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hasil Ranking -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4>Hasil Ranking Narapidana</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ranking</th>
                                            <th>Nama Narapidana</th>
                                            <th>Nomor Registrasi</th>
                                            <th>Nilai Preferensi (Ci)</th>
                                            <th>Jarak Positif (D+)</th>
                                            <th>Jarak Negatif (D-)</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($hasil_topsis['hasil'] as $item): ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-<?= $item['ranking'] == 1 ? 'success' : ($item['ranking'] <= 3 ? 'warning' : 'secondary') ?>">
                                                    <?= $item['ranking'] ?>
                                                </span>
                                            </td>
                                            <td><?= $item['nama'] ?></td>
                                            <td><?= $item['nomor_registrasi'] ?></td>
                                            <td>
                                                <span class="badge badge-<?= $item['nilai_preferensi'] >= 0.85 ? 'success' : ($item['nilai_preferensi'] >= 0.75 ? 'warning' : 'danger') ?>">
                                                    <?= number_format($item['nilai_preferensi'], 4) ?>
                                                </span>
                                            </td>
                                            <td><?= number_format($item['jarak_positif'], 4) ?></td>
                                            <td><?= number_format($item['jarak_negatif'], 4) ?></td>
                                            <td>
                                                <?php if ($item['status'] == 'Remisi Penuh'): ?>
                                                    <span class="badge badge-success"><?= $item['status'] ?></span>
                                                <?php elseif ($item['status'] == 'Remisi Separuh'): ?>
                                                    <span class="badge badge-warning"><?= $item['status'] ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger"><?= $item['status'] ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm btn-detail" data-id="<?= $item['narapidana_id'] ?>">
                                                    <i class="fas fa-calculator"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail Perhitungan -->
                    <div class="row">
                        <div class="col-12">
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
                                    <?php if (isset($hasil_topsis['detail']) && !empty($hasil_topsis['detail'])): ?>
                                        <!-- Matriks Keputusan -->
                                        <div class="mb-4">
                                            <h5>1. Matriks Keputusan</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Alternatif</th>
                                                            <?php if (isset($hasil_topsis['detail']['bobot'])): ?>
                                                            <?php foreach ($hasil_topsis['detail']['bobot'] as $kriteria): ?>
                                                            <th><?= $kriteria['kode'] ?? 'K' ?></th>
                                                            <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (isset($hasil_topsis['detail']['matriks_keputusan'])): ?>
                                                        <?php foreach ($hasil_topsis['detail']['matriks_keputusan'] as $index => $row): ?>
                                                        <tr>
                                                            <td>A<?= $index + 1 ?></td>
                                                            <?php foreach ($row as $nilai): ?>
                                                            <td><?= number_format($nilai, 2) ?></td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <!-- Matriks Normalisasi -->
                                        <div class="mb-4">
                                            <h5>2. Matriks Normalisasi</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Alternatif</th>
                                                            <?php if (isset($hasil_topsis['detail']['bobot'])): ?>
                                                            <?php foreach ($hasil_topsis['detail']['bobot'] as $kriteria): ?>
                                                            <th><?= $kriteria['kode'] ?? 'K' ?></th>
                                                            <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (isset($hasil_topsis['detail']['matriks_normalisasi'])): ?>
                                                        <?php foreach ($hasil_topsis['detail']['matriks_normalisasi'] as $index => $row): ?>
                                                        <tr>
                                                            <td>A<?= $index + 1 ?></td>
                                                            <?php foreach ($row as $nilai): ?>
                                                            <td><?= number_format($nilai, 4) ?></td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <!-- Bobot Kriteria -->
                                        <div class="mb-4">
                                            <h5>3. Bobot Kriteria</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Kode</th>
                                                            <th>Nama Kriteria</th>
                                                            <th>Jenis</th>
                                                            <th>Bobot</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (isset($hasil_topsis['detail']['bobot'])): ?>
                                                        <?php foreach ($hasil_topsis['detail']['bobot'] as $kriteria): ?>
                                                        <tr>
                                                            <td><?= $kriteria['kode'] ?? 'K' ?></td>
                                                            <td><?= $kriteria['nama'] ?? 'Kriteria' ?></td>
                                                            <td>
                                                                <span class="badge badge-<?= ($kriteria['jenis'] ?? 'Benefit') == 'Benefit' ? 'success' : 'danger' ?>">
                                                                    <?= $kriteria['jenis'] ?? 'Benefit' ?>
                                                                </span>
                                                            </td>
                                                            <td><?= number_format($kriteria['bobot'] ?? 0, 4) ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <!-- Matriks Terbobot -->
                                        <div class="mb-4">
                                            <h5>4. Matriks Terbobot</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Alternatif</th>
                                                            <?php if (isset($hasil_topsis['detail']['bobot'])): ?>
                                                            <?php foreach ($hasil_topsis['detail']['bobot'] as $kriteria): ?>
                                                            <th><?= $kriteria['kode'] ?? 'K' ?></th>
                                                            <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (isset($hasil_topsis['detail']['matriks_terbobot'])): ?>
                                                        <?php foreach ($hasil_topsis['detail']['matriks_terbobot'] as $index => $row): ?>
                                                        <tr>
                                                            <td>A<?= $index + 1 ?></td>
                                                            <?php foreach ($row as $nilai): ?>
                                                            <td><?= number_format($nilai, 4) ?></td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <!-- Solusi Ideal -->
                                        <div class="mb-4">
                                            <h5>5. Solusi Ideal</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-header bg-success">
                                                            <h6 class="card-title">Solusi Ideal Positif (A+)</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Kriteria</th>
                                                                        <th>Nilai</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php if (isset($hasil_topsis['detail']['solusi_ideal']['positif']) && isset($hasil_topsis['detail']['bobot'])): ?>
                                                                    <?php foreach ($hasil_topsis['detail']['solusi_ideal']['positif'] as $index => $nilai): ?>
                                                                    <tr>
                                                                        <td><?= $hasil_topsis['detail']['bobot'][$index]['kode'] ?? 'K' . ($index + 1) ?></td>
                                                                        <td><?= number_format($nilai, 4) ?></td>
                                                                    </tr>
                                                                    <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-header bg-danger">
                                                            <h6 class="card-title">Solusi Ideal Negatif (A-)</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Kriteria</th>
                                                                        <th>Nilai</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php if (isset($hasil_topsis['detail']['solusi_ideal']['negatif']) && isset($hasil_topsis['detail']['bobot'])): ?>
                                                                    <?php foreach ($hasil_topsis['detail']['solusi_ideal']['negatif'] as $index => $nilai): ?>
                                                                    <tr>
                                                                        <td><?= $hasil_topsis['detail']['bobot'][$index]['kode'] ?? 'K' . ($index + 1) ?></td>
                                                                        <td><?= number_format($nilai, 4) ?></td>
                                                                    </tr>
                                                                    <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Rumus Perhitungan -->
                                        <div class="mb-4">
                                            <h5>6. Rumus Perhitungan</h5>
                                            <div class="alert alert-info">
                                                <h6><i class="fas fa-calculator"></i> Nilai Preferensi (Ci)</h6>
                                                <p>Ci = D- / (D+ + D-)</p>
                                                <p>Dimana:</p>
                                                <ul>
                                                    <li>D+ = Jarak ke solusi ideal positif</li>
                                                    <li>D- = Jarak ke solusi ideal negatif</li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <!-- Kriteria Status -->
                                        <div class="mb-4">
                                            <h5>7. Kriteria Status Remisi</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Status</th>
                                                            <th>Nilai Preferensi (Ci)</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="table-success">
                                                            <td><strong>Remisi Penuh</strong></td>
                                                            <td>Ci ≥ 0.8500</td>
                                                            <td>Narapidana terbaik</td>
                                                        </tr>
                                                        <tr class="table-warning">
                                                            <td><strong>Remisi Separuh</strong></td>
                                                            <td>0.7500 ≤ Ci < 0.8500</td>
                                                            <td>Narapidana rata-rata</td>
                                                        </tr>
                                                        <tr class="table-danger">
                                                            <td><strong>Tidak Layak</strong></td>
                                                            <td>Ci < 0.7500</td>
                                                            <td>Perlu perhatian khusus</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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
                            <a href="<?= base_url('topsis') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <a href="<?= base_url('topsis/riwayat?periode_id=' . $periode['id']) ?>" class="btn btn-info">
                                <i class="fas fa-history"></i> Lihat Riwayat
                            </a>
                            <a href="<?= base_url('topsis/exportPdf/' . $periode['id']) ?>" class="btn btn-success" target="_blank">
                                <i class="fas fa-file-pdf"></i> Cetak Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        // Detail perhitungan untuk narapidana tertentu
        $('.btn-detail').click(function() {
            const narapidanaId = $(this).data('id');
            // Implementasi modal detail per narapidana
            alert('Detail perhitungan untuk narapidana ID: ' + narapidanaId);
        });
        
        // Auto-collapse detail perhitungan jika terlalu panjang
        $('.card-body .collapse').on('shown.bs.collapse', function() {
            $(this).parent().find('.fa-minus').removeClass('fa-minus').addClass('fa-plus');
        }).on('hidden.bs.collapse', function() {
            $(this).parent().find('.fa-plus').removeClass('fa-plus').addClass('fa-minus');
        });
    });
    </script>
<?= $this->endSection() ?>