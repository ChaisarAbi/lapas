<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'riwayat-validasi';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('kalapas/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Riwayat Validasi</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Validasi Penilaian Narapidana</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <button type="button" class="btn btn-default btn-sm" onclick="window.print()">
                                <i class="fas fa-print"></i> Cetak
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($riwayat)): ?>
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Data Tidak Tersedia</h5>
                            <p>Belum ada riwayat validasi yang tersimpan.</p>
                            <a href="<?= base_url('kalapas/validasi') ?>" class="btn btn-primary mt-2">
                                <i class="fas fa-check-circle"></i> Lakukan Validasi
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i> Menampilkan semua riwayat validasi (<?= count($riwayat) ?> data)
                        </div>
                        
                        <!-- Filter Periode -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Filter Riwayat</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Periode</label>
                                                    <select id="filterPeriode" class="form-control">
                                                        <option value="">Semua Periode</option>
                                                        <?php 
                                                        $periodes = array_unique(array_column($riwayat, 'periode'));
                                                        sort($periodes);
                                                        foreach ($periodes as $p): ?>
                                                            <option value="<?= $p ?>"><?= $p ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Status Validasi</label>
                                                    <select id="filterStatus" class="form-control">
                                                        <option value="">Semua Status</option>
                                                        <option value="disetujui">Disetujui</option>
                                                        <option value="perlu_review">Perlu Review</option>
                                                        <option value="ditolak">Ditolak</option>
                                                        <option value="menunggu">Menunggu</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-primary btn-block" onclick="filterRiwayat()">
                                                        <i class="fas fa-filter"></i> Filter
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabel Riwayat Validasi -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tabelRiwayat">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Tanggal & Waktu</th>
                                        <th width="10%">Periode</th>
                                        <th width="20%">Narapidana</th>
                                        <th width="15%">Nomor Registrasi</th>
                                        <th width="15%">Status Validasi</th>
                                        <th width="15%">Validator</th>
                                        <th width="15%">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($riwayat as $index => $item): ?>
                                    <tr data-periode="<?= $item['periode'] ?>" data-status="<?= $item['status_validasi'] ?>">
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <strong><?= date('d/m/Y', strtotime($item['created_at'])) ?></strong><br>
                                            <small class="text-muted"><?= date('H:i:s', strtotime($item['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?= $item['periode'] ?></span>
                                        </td>
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
                                        <td><?= $item['validator_nama'] ?: 'Sistem' ?></td>
                                        <td><?= $item['catatan'] ?: '-' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Statistik -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Statistik Riwayat Validasi</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php 
                                            $total = count($riwayat);
                                            $disetujui = array_filter($riwayat, function($item) {
                                                return $item['status_validasi'] == 'disetujui';
                                            });
                                            $perluReview = array_filter($riwayat, function($item) {
                                                return $item['status_validasi'] == 'perlu_review';
                                            });
                                            $ditolak = array_filter($riwayat, function($item) {
                                                return $item['status_validasi'] == 'ditolak';
                                            });
                                            $menunggu = array_filter($riwayat, function($item) {
                                                return $item['status_validasi'] == 'menunggu';
                                            });
                                            ?>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-history"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Riwayat</span>
                                                        <span class="info-box-number"><?= $total ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: 100%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            Semua periode
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-success">
                                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Disetujui</span>
                                                        <span class="info-box-number"><?= count($disetujui) ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: <?= $total > 0 ? (count($disetujui) / $total) * 100 : 0 ?>%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            <?= $total > 0 ? round((count($disetujui) / $total) * 100, 1) : 0 ?>% dari total
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-warning">
                                                    <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Perlu Review</span>
                                                        <span class="info-box-number"><?= count($perluReview) ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: <?= $total > 0 ? (count($perluReview) / $total) * 100 : 0 ?>%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            <?= $total > 0 ? round((count($perluReview) / $total) * 100, 1) : 0 ?>% dari total
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-danger">
                                                    <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Ditolak</span>
                                                        <span class="info-box-number"><?= count($ditolak) ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: <?= $total > 0 ? (count($ditolak) / $total) * 100 : 0 ?>%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            <?= $total > 0 ? round((count($ditolak) / $total) * 100, 1) : 0 ?>% dari total
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- JavaScript untuk Filter -->
                        <script>
                        function filterRiwayat() {
                            const periode = document.getElementById('filterPeriode').value;
                            const status = document.getElementById('filterStatus').value;
                            const rows = document.querySelectorAll('#tabelRiwayat tbody tr');
                            
                            rows.forEach(row => {
                                const rowPeriode = row.getAttribute('data-periode');
                                const rowStatus = row.getAttribute('data-status');
                                
                                let show = true;
                                
                                if (periode && rowPeriode !== periode) {
                                    show = false;
                                }
                                
                                if (status && rowStatus !== status) {
                                    show = false;
                                }
                                
                                row.style.display = show ? '' : 'none';
                            });
                            
                            // Update nomor urut
                            let counter = 1;
                            rows.forEach(row => {
                                if (row.style.display !== 'none') {
                                    row.cells[0].textContent = counter++;
                                }
                            });
                        }
                        </script>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i> Riwayat validasi mencatat semua aktivitas validasi yang dilakukan oleh Kepala Lapas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
