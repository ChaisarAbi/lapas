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
                                    <h3 class="card-title">Klasifikasi Narapidana</h3>
                                </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="periode">Pilih Periode:</label>
                                    <select name="periode" id="periode" class="form-control" onchange="updateLaporan()">
                                        <?php foreach ($periode_list as $p): ?>
                                            <option value="<?= $p ?>" <?= $p == $periode ? 'selected' : '' ?>>
                                                <?= date('F Y', strtotime($p . '-01')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="petugas">Pilih Petugas:</label>
                                    <select name="petugas" id="petugas" class="form-control" onchange="updateLaporan()">
                                        <option value="">Semua Petugas</option>
                                        <?php foreach ($petugas_list as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= $p['nama'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary" onclick="previewRanking()">
                                        <i class="fas fa-eye"></i> Preview Ranking
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="cetakRanking()">
                                        <i class="fas fa-print"></i> Cetak Ranking
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Distribution Cards -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-success-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">Remisi Penuh</h5>
                                                <p class="card-text">30% Terbaik</p>
                                            </div>
                                            <div class="text-right">
                                                <i class="fas fa-star fa-2x text-success"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge badge-success badge-lg">30%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">Remisi Separuh</h5>
                                                <p class="card-text">30% Berikutnya</p>
                                            </div>
                                            <div class="text-right">
                                                <i class="fas fa-star-half-alt fa-2x text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge badge-warning badge-lg">30%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">Tidak Layak</h5>
                                                <p class="card-text">40% Terbawah</p>
                                            </div>
                                            <div class="text-right">
                                                <i class="fas fa-times fa-2x text-danger"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge badge-danger badge-lg">40%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Legend -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Sistem Status Remisi</h6>
                                    <ul class="mb-0">
                                        <li><strong>Remisi Penuh:</strong> 30% narapidana dengan nilai tertinggi</li>
                                        <li><strong>Remisi Separuh:</strong> 30% narapidana berikutnya</li>
                                        <li><strong>Tidak Layak:</strong> 40% narapidana dengan nilai terendah</li>
                                        <li>Status ditentukan berdasarkan peringkat ranking setelah perhitungan ANP-TOPSIS</li>
                                    </ul>
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
                                    <h3 class="card-title">Panduan Laporan</h3>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li><strong>Preview Laporan:</strong> Selalu preview laporan terlebih dahulu untuk memastikan data sudah benar.</li>
                                        <li><strong>Periode:</strong> Pastikan memilih periode yang sesuai dengan data yang ingin dicetak.</li>
                                        <li><strong>Klasifikasi:</strong> Laporan akan menampilkan klasifikasi narapidana berdasarkan nilai preferensi (Ci).</li>
                                        <li><strong>Status Remisi:</strong> Setiap narapidana akan mendapatkan status remisi sesuai dengan klasifikasi.</li>
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
