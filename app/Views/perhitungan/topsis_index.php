<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'topsis';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Perhitungan TOPSIS</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Perhitungan Metode TOPSIS</h3>
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
                    
                    <!-- Informasi Sistem -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="callout callout-info">
                                <h5><i class="fas fa-info-circle"></i> Informasi Sistem TOPSIS</h5>
                                <p>Sistem ini menggunakan metode <strong>TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)</strong> untuk menentukan ranking narapidana berdasarkan kriteria penilaian.</p>
                                <p><strong>Alur Perhitungan:</strong></p>
                                <ol>
                                    <li>Konversi nilai 0-100 ke skala 1-5 (Benefit) atau 1-3 (Cost)</li>
                                    <li>Normalisasi matriks keputusan</li>
                                    <li>Matriks terbobot menggunakan bobot dari ANP</li>
                                    <li>Menentukan solusi ideal positif dan negatif</li>
                                    <li>Menghitung jarak ke solusi ideal</li>
                                    <li>Menghitung nilai preferensi (Ci)</li>
                                    <li>Menentukan ranking dan status remisi</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Perhitungan -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Hitung TOPSIS</h3>
                                </div>
                                <form method="post" action="<?= base_url('bimkesmaswat/topsis/hitung') ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="periode_id">Pilih Periode Penilaian</label>
                                            <select name="periode_id" id="periode_id" class="form-control" required>
                                                <option value="">Pilih Periode</option>
                                                <?php foreach ($periode_list as $periode): ?>
                                                <option value="<?= $periode['id'] ?>" <?= $periode_aktif && $periode['id'] == $periode_aktif['id'] ? 'selected' : '' ?>>
                                                    <?= $periode['nama_periode'] ?> (<?= $periode['tahun'] ?>-<?= str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT) ?>)
                                                    <?= $periode['status'] == 'aktif' ? ' - Aktif' : '' ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if ($periode_aktif): ?>
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i> Periode aktif: <?= $periode_aktif['nama_periode'] ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Informasi Kriteria -->
                                        <div class="form-group">
                                            <label>Informasi Kriteria</label>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Kode</th>
                                                            <th>Nama Kriteria</th>
                                                            <th>Jenis</th>
                                                            <th>Skala Konversi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        // Ambil data kriteria untuk informasi
                                                        $kriteriaModel = new \App\Models\KriteriaModel();
                                                        $kriteria = $kriteriaModel->getOrdered();
                                                        ?>
                                                        <?php foreach ($kriteria as $k): ?>
                                                        <tr>
                                                            <td><?= $k['kode'] ?></td>
                                                            <td><?= $k['nama'] ?></td>
                                                            <td>
                                                                <span class="badge badge-<?= $k['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                                                                    <?= $k['jenis'] ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php if ($k['jenis'] == 'Benefit'): ?>
                                                                    0-20=1, 21-40=2, 41-60=3, 61-80=4, 81-100=5
                                                                <?php else: ?>
                                                                    0-33=1, 34-66=2, 67-100=3
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <!-- Status Remisi -->
                                        <div class="form-group">
                                            <label>Kriteria Status Remisi</label>
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
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-calculator"></i> Hitung TOPSIS
                                        </button>
                                        <a href="<?= base_url('bimkesmaswat/topsis/riwayat') ?>" class="btn btn-info">
                                            <i class="fas fa-history"></i> Lihat Riwayat
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Sidebar Informasi -->
                        <div class="col-md-4">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">Persyaratan</h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <h5><i class="fas fa-exclamation-triangle"></i> Perhatian!</h5>
                                        <p>Sebelum menghitung TOPSIS, pastikan:</p>
                                        <ul>
                                            <li>Data penilaian sudah lengkap untuk periode yang dipilih</li>
                                            <li>Bobot ANP sudah dihitung oleh TPP</li>
                                            <li>Semua narapidana aktif sudah dinilai</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-info"><i class="fas fa-database"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Data Penilaian</span>
                                            <span class="info-box-number">Periode Aktif</span>
                                            <div class="progress">
                                                <div class="progress-bar bg-info" style="width: 70%"></div>
                                            </div>
                                            <span class="progress-description">
                                                Cek kelengkapan data
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-success"><i class="fas fa-balance-scale"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Bobot ANP</span>
                                            <span class="info-box-number">Dari TPP</span>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" style="width: 90%"></div>
                                            </div>
                                            <span class="progress-description">
                                                Bobot sudah tersedia
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="<?= base_url('bimkesmaswat/penilaian') ?>" class="btn btn-success">
                                <i class="fas fa-edit"></i> Input Nilai Penilaian
                            </a>
                            <a href="<?= base_url('bimkesmaswat/penilaian/riwayat') ?>" class="btn btn-info">
                                <i class="fas fa-history"></i> Riwayat Penilaian
                            </a>
                            <a href="<?= base_url('bimkesmaswat/dashboard') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        // Validasi sebelum menghitung
        $('form').submit(function(e) {
            const periodeId = $('#periode_id').val();
            if (!periodeId) {
                e.preventDefault();
                alert('Silakan pilih periode penilaian terlebih dahulu!');
                return false;
            }
            
            // Konfirmasi
            if (!confirm('Apakah Anda yakin ingin menghitung TOPSIS untuk periode ini? Perhitungan akan memakan waktu beberapa saat.')) {
                e.preventDefault();
                return false;
            }
            
            // Tampilkan loading
            $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Menghitung...').prop('disabled', true);
        });
    });
    </script>
<?= $this->endSection() ?>