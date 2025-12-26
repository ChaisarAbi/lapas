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
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Laporan Ranking Narapidana</h3>
                                </div>
                                <div class="card-body">
                                    <p>Laporan ranking narapidana berdasarkan perhitungan TOPSIS. Laporan ini menampilkan peringkat narapidana berdasarkan nilai akhir dari proses penilaian.</p>
                                    <form method="get" action="<?= base_url('admin/laporan/preview-ranking') ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Periode</label>
                                                    <select name="periode" class="form-control" required>
                                                        <option value="">Pilih Periode</option>
                                                        <?php foreach ($periode_list as $p): ?>
                                                            <option value="<?= $p ?>"><?= $p ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button type="submit" class="btn btn-primary btn-block">
                                                        <i class="fas fa-eye"></i> Preview Laporan Ranking
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info-circle"></i> Informasi</h5>
                                <p>Laporan validasi dan penilaian petugas telah dihapus sesuai permintaan. Hanya laporan ranking yang tersedia untuk saat ini.</p>
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
