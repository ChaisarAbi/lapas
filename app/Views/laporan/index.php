<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'laporan';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Manajemen Laporan</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manajemen Laporan</h3>
                </div>
                <div class="card-body">
                    <p>Pilih jenis laporan yang ingin Anda buat. Anda dapat melihat preview terlebih dahulu sebelum mencetak.</p>
                    
                    <div class="row">
                        <!-- Laporan Ranking -->
                        <div class="col-md-4">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Laporan Ranking</h3>
                                </div>
                                <div class="card-body">
                                    <p>Laporan ranking narapidana berdasarkan perhitungan TOPSIS.</p>
                                    <form method="get" action="<?= base_url('admin/laporan/preview-ranking') ?>">
                                        <div class="form-group">
                                            <label>Periode</label>
                                            <select name="periode" class="form-control" required>
                                                <option value="">Pilih Periode</option>
                                                <?php foreach ($periode_list as $p): ?>
                                                    <option value="<?= $p ?>"><?= $p ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-eye"></i> Preview Laporan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Laporan Validasi -->
                        <div class="col-md-4">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Laporan Validasi</h3>
                                </div>
                                <div class="card-body">
                                    <p>Laporan hasil validasi oleh Kepala Lapas.</p>
                                    <form method="get" action="<?= base_url('admin/laporan/preview-validasi') ?>">
                                        <div class="form-group">
                                            <label>Periode</label>
                                            <select name="periode" class="form-control" required>
                                                <option value="">Pilih Periode</option>
                                                <?php foreach ($periode_list as $p): ?>
                                                    <option value="<?= $p ?>"><?= $p ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-eye"></i> Preview Laporan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Laporan Penilaian Petugas -->
                        <div class="col-md-4">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Laporan Penilaian Petugas</h3>
                                </div>
                                <div class="card-body">
                                    <p>Laporan penilaian per petugas BIMKEMASWAT.</p>
                                    <form method="get" action="<?= base_url('admin/laporan/preview-penilaian-petugas') ?>">
                                        <div class="form-group">
                                            <label>Periode</label>
                                            <select name="periode" class="form-control" required>
                                                <option value="">Pilih Periode</option>
                                                <?php foreach ($periode_list as $p): ?>
                                                    <option value="<?= $p ?>"><?= $p ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Petugas (Opsional)</label>
                                            <select name="petugas_id" class="form-control">
                                                <option value="">Semua Petugas</option>
                                                <?php foreach ($petugas_list as $petugas): ?>
                                                    <option value="<?= $petugas['id'] ?>"><?= $petugas['nama_lengkap'] ?> (<?= $petugas['username'] ?>)</option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-block">
                                            <i class="fas fa-eye"></i> Preview Laporan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Panduan Laporan</h3>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li><strong>Preview Laporan:</strong> Selalu preview laporan terlebih dahulu untuk memastikan data sudah benar.</li>
                                        <li><strong>Periode:</strong> Pastikan memilih periode yang sesuai dengan data yang ingin dicetak.</li>
                                        <li><strong>Cetak:</strong> Setelah preview, Anda dapat mencetak laporan dalam format PDF.</li>
                                        <li><strong>Arsip:</strong> Simpan laporan yang telah dicetak untuk dokumentasi.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
