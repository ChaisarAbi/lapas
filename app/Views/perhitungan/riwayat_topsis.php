<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'topsis-riwayat';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/topsis') ?>">Perhitungan TOPSIS</a></li>
    <li class="breadcrumb-item active">Riwayat Hasil</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Hasil Perhitungan TOPSIS</h3>
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
                    
                    <!-- Filter Periode -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="get" action="<?= base_url('bimkesmaswat/topsis/riwayat') ?>">
                                <div class="input-group">
                                    <select name="periode_id" id="periode_id" class="form-control">
                                        <option value="">Semua Periode</option>
                                        <?php foreach ($periode_list as $periode): ?>
                                        <option value="<?= $periode['id'] ?>" <?= $selected_periode == $periode['id'] ? 'selected' : '' ?>>
                                            <?= $periode['nama_periode'] ?> (<?= $periode['tahun'] ?>-<?= str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                        <a href="<?= base_url('bimkesmaswat/topsis/riwayat') ?>" class="btn btn-secondary">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="<?= base_url('bimkesmaswat/topsis') ?>" class="btn btn-success">
                                <i class="fas fa-calculator"></i> Hitung TOPSIS Baru
                            </a>
                        </div>
                    </div>
                    
                    <?php if (empty($hasil_topsis)): ?>
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Informasi</h5>
                            <p>Tidak ada data hasil TOPSIS untuk periode yang dipilih.</p>
                            <p>Silakan <a href="<?= base_url('bimkesmaswat/topsis') ?>">hitung TOPSIS</a> terlebih dahulu.</p>
                        </div>
                    <?php else: ?>
                        <!-- Statistik -->
                        <div class="row mb-4">
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Narapidana</span>
                                        <span class="info-box-number"><?= count($hasil_topsis) ?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Periode: <?= $hasil_topsis[0]['nama_periode'] ?? '-' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Remisi Penuh</span>
                                        <span class="info-box-number">
                                            <?= count(array_filter($hasil_topsis, function($item) { return $item['status'] == 'Remisi Penuh'; })) ?>
                                        </span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= count($hasil_topsis) > 0 ? (count(array_filter($hasil_topsis, function($item) { return $item['status'] == 'Remisi Penuh'; })) / count($hasil_topsis) * 100) : 0 ?>%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Narapidana terbaik
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Remisi Separuh</span>
                                        <span class="info-box-number">
                                            <?= count(array_filter($hasil_topsis, function($item) { return $item['status'] == 'Remisi Separuh'; })) ?>
                                        </span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= count($hasil_topsis) > 0 ? (count(array_filter($hasil_topsis, function($item) { return $item['status'] == 'Remisi Separuh'; })) / count($hasil_topsis) * 100) : 0 ?>%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Rata-rata
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Tidak Layak</span>
                                        <span class="info-box-number">
                                            <?= count(array_filter($hasil_topsis, function($item) { return $item['status'] == 'Tidak Layak'; })) ?>
                                        </span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= count($hasil_topsis) > 0 ? (count(array_filter($hasil_topsis, function($item) { return $item['status'] == 'Tidak Layak'; })) / count($hasil_topsis) * 100) : 0 ?>%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Perlu perhatian
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabel Hasil -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Hasil Ranking TOPSIS</h3>
                                        <div class="card-tools">
                                            <?php if ($selected_periode): ?>
                                            <a href="<?= base_url('bimkesmaswat/topsis/exportPdf/' . $selected_periode) ?>" class="btn btn-success btn-sm" target="_blank">
                                                <i class="fas fa-file-pdf"></i> Export PDF
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Ranking</th>
                                                        <th>Nama Narapidana</th>
                                                        <th>Nomor Registrasi</th>
                                                        <th>Nilai Preferensi (Ci)</th>
                                                        <th>Status</th>
                                                        <th>Tanggal Hitung</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($hasil_topsis as $item): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-<?= $item['ranking'] == 1 ? 'success' : ($item['ranking'] <= 3 ? 'warning' : 'secondary') ?>">
                                                                <?= $item['ranking'] ?>
                                                            </span>
                                                        </td>
                                                        <td><?= $item['nama_lengkap'] ?></td>
                                                        <td><?= $item['nomor_registrasi'] ?></td>
                                                        <td>
                                                            <span class="badge badge-<?= $item['nilai_preferensi'] >= 0.85 ? 'success' : ($item['nilai_preferensi'] >= 0.75 ? 'warning' : 'danger') ?>">
                                                                <?= number_format($item['nilai_preferensi'], 4) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($item['status'] == 'Remisi Penuh'): ?>
                                                                <span class="badge badge-success"><?= $item['status'] ?></span>
                                                            <?php elseif ($item['status'] == 'Remisi Separuh'): ?>
                                                                <span class="badge badge-warning"><?= $item['status'] ?></span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger"><?= $item['status'] ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                                                        <td>
                                                            <a href="<?= base_url('bimkesmaswat/topsis/detail/' . $item['id']) ?>" class="btn btn-info btn-sm">
                                                                <i class="fas fa-eye"></i> Detail
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Grafik Distribusi Status -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Distribusi Status Remisi</h3>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="statusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Top 5 Narapidana</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Ranking</th>
                                                        <th>Nama</th>
                                                        <th>Nilai (Ci)</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $top5 = array_slice($hasil_topsis, 0, 5);
                                                    foreach ($top5 as $item): 
                                                    ?>
                                                    <tr>
                                                        <td><span class="badge badge-primary"><?= $item['ranking'] ?></span></td>
                                                        <td><?= $item['nama_lengkap'] ?></td>
                                                        <td><?= number_format($item['nilai_preferensi'], 4) ?></td>
                                                        <td>
                                                            <?php if ($item['status'] == 'Remisi Penuh'): ?>
                                                                <span class="badge badge-success"><?= $item['status'] ?></span>
                                                            <?php elseif ($item['status'] == 'Remisi Separuh'): ?>
                                                                <span class="badge badge-warning"><?= $item['status'] ?></span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger"><?= $item['status'] ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Tombol Aksi -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="<?= base_url('bimkesmaswat/topsis') ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Perhitungan
                            </a>
                            <a href="<?= base_url('bimkesmaswat/dashboard') ?>" class="btn btn-secondary">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    $(document).ready(function() {
        <?php if (!empty($hasil_topsis)): ?>
        // Data untuk chart
        const statusData = {
            labels: ['Remisi Penuh', 'Remisi Separuh', 'Tidak Layak'],
            datasets: [{
                data: [
                    <?= count(array_filter($hasil_topsis, function($item) { return $item['status'] == 'Remisi Penuh'; })) ?>,
                    <?= count(array_filter($hasil_topsis, function($item) { return $item['status'] == 'Remisi Separuh'; })) ?>,
                    <?= count(array_filter($hasil_topsis, function($item) { return $item['status'] == 'Tidak Layak'; })) ?>
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderColor: [
                    '#218838',
                    '#e0a800',
                    '#c82333'
                ],
                borderWidth: 1
            }]
        };
        
        // Konfigurasi chart
        const ctx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(ctx, {
            type: 'doughnut',
            data: statusData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    });
    </script>
<?= $this->endSection() ?>