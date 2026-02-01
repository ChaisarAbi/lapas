<?= $this->extend('layouts/dashboard_template') ?>

<style>
/* Soft colors for better visual experience */
.bg-success-light {
    background-color: rgba(144, 238, 144, 0.3) !important; /* Light green */
}
.bg-warning-light {
    background-color: rgba(255, 255, 224, 0.4) !important; /* Light yellow */
}
.bg-danger-light {
    background-color: rgba(255, 182, 193, 0.3) !important; /* Light pink */
}
.bg-info-light {
    background-color: rgba(173, 216, 230, 0.3) !important; /* Light blue */
}
.bg-primary-light {
    background-color: rgba(135, 206, 250, 0.3) !important; /* Light sky blue */
}

.table-hover tbody tr:hover td {
    background-color: rgba(0, 0, 0, 0.05);
}

.info-box {
    min-height: 80px;
    margin-bottom: 0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.info-box-icon {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    border-radius: 8px 0 0 8px;
}
.info-box-content {
    padding: 10px;
}

/* Improved calculation matrix styling */
.calculation-matrix {
    border-collapse: separate;
    border-spacing: 2px;
}
.calculation-matrix th {
    background-color: #f8f9fa;
    font-weight: 600;
    padding: 8px 12px;
}
.calculation-matrix td {
    padding: 10px 12px;
    text-align: center;
    border: 1px solid #dee2e6;
}
.calculation-matrix .matrix-diagonal {
    background-color: #f8f9fa;
    font-weight: bold;
    color: #495057;
}
.calculation-matrix .matrix-value {
    background-color: #e9f7ef; /* Soft green for values */
    color: #155724;
    font-weight: 500;
}
.calculation-matrix .matrix-reciprocal {
    background-color: #fff3cd; /* Soft yellow for reciprocal */
    color: #856404;
    font-weight: 500;
}

/* Soft text colors */
.text-soft-success {
    color: #28a745 !important;
}
.text-soft-warning {
    color: #ffc107 !important;
}
.text-soft-danger {
    color: #dc3545 !important;
}
.text-soft-info {
    color: #17a2b8 !important;
}

/* Card improvements */
.card {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.card-header {
    border-radius: 10px 10px 0 0 !important;
}
</style>

<?= $this->section('sidebar_menu') ?>
    <li class="nav-item">
        <a href="<?= base_url('tpp/dashboard') ?>" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-header">ANALYTIC NETWORK PROCESS</li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/kriteria') ?>" class="nav-link">
            <i class="nav-icon fas fa-balance-scale"></i>
            <p>Kelola Kriteria</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/bobot') ?>" class="nav-link">
            <i class="nav-icon fas fa-calculator"></i>
            <p>Input Bobot</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/bobot/matriks') ?>" class="nav-link">
            <i class="nav-icon fas fa-check-circle"></i>
            <p>Validasi Konsistensi</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/anp/pairwise-comparison') ?>" class="nav-link active">
            <i class="nav-icon fas fa-project-diagram"></i>
            <p>Pairwise Comparison</p>
        </a>
    </li>
    <li class="nav-header">LAPORAN</li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/anp') ?>" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Hasil ANP</p>
        </a>
    </li>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/anp') ?>">Hasil ANP</a></li>
    <li class="breadcrumb-item active">Pairwise Comparison ANP</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pairwise Comparison Analytic Network Process (ANP)</h3>
                    <div class="card-tools">
                        <?php if ($periode): ?>
                            <span class="badge badge-info">Periode: <?= $periode['nama_periode'] ?></span>
                        <?php endif; ?>
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
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Tentang Pairwise Comparison ANP</h5>
                        <p>Pairwise comparison digunakan untuk menentukan tingkat kepentingan relatif antar subkriteria dalam Analytic Network Process (ANP).</p>
                        <p><strong>Skala Saaty (1-9):</strong></p>
                        <ul>
                            <li>1: Sama pentingnya</li>
                            <li>3: Sedikit lebih penting</li>
                            <li>5: Lebih penting</li>
                            <li>7: Sangat lebih penting</li>
                            <li>9: Mutlak lebih penting</li>
                        </ul>
                        <p>Nilai kebalikan (reciprocal) akan diisi otomatis. Misalnya jika A vs B = 3, maka B vs A = 1/3.</p>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Progress Pairwise Comparison</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="progress-group">
                                                <span class="progress-text">Pairwise Terisi</span>
                                                <span class="float-right">
                                                    <b id="filled-count">0</b> / <span id="total-count">0</span>
                                                </span>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-success" id="progress-bar" style="width: 0%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="progress-group">
                                                <span class="progress-text">Tingkat Kelengkapan</span>
                                                <span class="float-right">
                                                    <b id="completion-percentage">0</b>%
                                                </span>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-info" id="completion-bar" style="width: 0%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-lightbulb"></i> 
                                            <strong>Saran:</strong> Minimal 70% pairwise harus terisi untuk hasil ANP yang akurat.
                                            Gunakan tombol "Auto Fill" untuk mengisi nilai default pada pairwise yang kosong.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Aksi Cepat</h5>
                                </div>
                                <div class="card-body">
                                    <button type="button" class="btn btn-warning btn-block mb-2" id="auto-fill-btn">
                                        <i class="fas fa-bolt"></i> Auto Fill Kosong
                                    </button>
                                    <button type="button" class="btn btn-danger btn-block mb-2" id="auto-fill-all-btn">
                                        <i class="fas fa-bullseye"></i> Auto Fill Semua
                                    </button>
                                    <button type="button" class="btn btn-primary btn-block mb-2" id="hitung-anp-btn">
                                        <i class="fas fa-calculator"></i> Hitung ANP
                                    </button>
                                    <a href="<?= base_url('tpp/anp') ?>" class="btn btn-info btn-block">
                                        <i class="fas fa-eye"></i> Lihat Hasil ANP
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Input Pairwise Node ke Node -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Input Pairwise Comparison (Node ke Node)</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('tpp/anp/simpan-pairwise') ?>" method="post" id="pairwise-form">
                                <?= csrf_field() ?>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cluster_dari">Cluster Dari (Yang Mempengaruhi):</label>
                                            <select name="cluster_dari" id="cluster_dari" class="form-control" required onchange="updateNodesFromCluster('dari')">
                                                <option value="">-- Pilih Cluster --</option>
                                                <?php 
                                                // Group subkriteria by kriteria (cluster)
                                                $clusters = [];
                                                foreach ($subkriteria as $sk) {
                                                    $clusterId = $sk['kriteria_id'];
                                                    if (!isset($clusters[$clusterId])) {
                                                        $clusters[$clusterId] = [
                                                            'id' => $clusterId,
                                                            'nama' => $sk['kriteria_nama'],
                                                            'nodes' => []
                                                        ];
                                                    }
                                                    $clusters[$clusterId]['nodes'][] = $sk;
                                                }
                                                ?>
                                                <?php foreach ($clusters as $cluster): ?>
                                                <option value="<?= $cluster['id'] ?>">
                                                    <?= $cluster['nama'] ?> (<?= count($cluster['nodes']) ?> node)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cluster_ke">Cluster Ke (Yang Dipengaruhi):</label>
                                            <select name="cluster_ke" id="cluster_ke" class="form-control" required onchange="updateNodesFromCluster('ke')">
                                                <option value="">-- Pilih Cluster --</option>
                                                <?php foreach ($clusters as $cluster): ?>
                                                <option value="<?= $cluster['id'] ?>">
                                                    <?= $cluster['nama'] ?> (<?= count($cluster['nodes']) ?> node)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="node_dari">Node Dari (Anggota Cluster):</label>
                                            <select name="node_dari" id="node_dari" class="form-control" required>
                                                <option value="">-- Pilih Cluster terlebih dahulu --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="node_ke">Node Ke (Anggota Cluster):</label>
                                            <select name="node_ke" id="node_ke" class="form-control" required>
                                                <option value="">-- Pilih Cluster terlebih dahulu --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="skala">Skala Saaty (1-9):</label>
                                            <select name="skala" id="skala" class="form-control" required onchange="updateCalculation()">
                                                <option value="">-- Pilih Skala --</option>
                                                <option value="1">1 - Sama pentingnya</option>
                                                <option value="2">2 - Antara sama dan sedikit lebih penting</option>
                                                <option value="3">3 - Sedikit lebih penting</option>
                                                <option value="4">4 - Antara sedikit dan lebih penting</option>
                                                <option value="5">5 - Lebih penting</option>
                                                <option value="6">6 - Antara lebih dan sangat lebih penting</option>
                                                <option value="7">7 - Sangat lebih penting</option>
                                                <option value="8">8 - Antara sangat dan mutlak lebih penting</option>
                                                <option value="9">9 - Mutlak lebih penting</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Perhitungan Matematika -->
                                <div class="row mt-4" id="calculation-section" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">Perhitungan Matematika</h3>
                                            </div>
                                            <div class="card-body">
                                                <div id="calculation-content">
                                                    <!-- Perhitungan akan ditampilkan di sini -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Tambah Pairwise
                                        </button>
                                        <button type="button" class="btn btn-info" onclick="autoFillMatrix()">
                                            <i class="fas fa-magic"></i> Auto Fill Matriks
                                        </button>
                                        <a href="<?= base_url('tpp/anp') ?>" class="btn btn-success">
                                            <i class="fas fa-calculator"></i> Hitung ANP
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tabel Histori Pairwise -->
                    <div class="card card-success mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Histori Pairwise Comparison</h3>
                            <div class="card-tools">
                                <span class="badge badge-info">Total: <?= count($histori_pairwise ?? []) ?> entri</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($histori_pairwise)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="20%">Node Dari</th>
                                            <th width="20%">Node Ke</th>
                                            <th width="15%">Skala</th>
                                            <th width="20%">Keterangan</th>
                                            <th width="15%">Tanggal</th>
                                            <th width="5%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($histori_pairwise as $index => $histori): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    <?= $histori['node_dari_kode'] ?? 'N/A' ?>
                                                </span><br>
                                                <small><?= $histori['node_dari_nama'] ?? '' ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?= $histori['node_ke_kode'] ?? 'N/A' ?>
                                                </span><br>
                                                <small><?= $histori['node_ke_nama'] ?? '' ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $histori['skala'] == 1 ? 'secondary' : 
                                                    ($histori['skala'] <= 3 ? 'success' : 
                                                    ($histori['skala'] <= 5 ? 'warning' : 'danger'))
                                                ?>">
                                                    <?= $histori['skala'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $keterangan = '';
                                                switch($histori['skala']) {
                                                    case 1: $keterangan = 'Sama pentingnya'; break;
                                                    case 2: $keterangan = 'Antara sama dan sedikit lebih penting'; break;
                                                    case 3: $keterangan = 'Sedikit lebih penting'; break;
                                                    case 4: $keterangan = 'Antara sedikit dan lebih penting'; break;
                                                    case 5: $keterangan = 'Lebih penting'; break;
                                                    case 6: $keterangan = 'Antara lebih dan sangat lebih penting'; break;
                                                    case 7: $keterangan = 'Sangat lebih penting'; break;
                                                    case 8: $keterangan = 'Antara sangat dan mutlak lebih penting'; break;
                                                    case 9: $keterangan = 'Mutlak lebih penting'; break;
                                                    default: $keterangan = 'Tidak diketahui';
                                                }
                                                echo $keterangan;
                                                ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($histori['created_at'] ?? 'now')) ?></td>
                                            <td>
                                                <form action="<?= base_url('tpp/anp/hapus-pairwise/' . $histori['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus pairwise ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="icon fas fa-exclamation-triangle"></i> Belum ada data pairwise comparison.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Tabel Matriks Interdependensi -->
                    <div class="card card-info mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Matriks Interdependensi ANP</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($matriks_interdependensi)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Keterangan Matriks:</strong> 
                                Nilai menunjukkan seberapa penting node baris terhadap node kolom. 
                                Nilai > 1 = lebih penting, nilai < 1 = kurang penting, nilai = 1 = sama penting.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="15%">Node (Baris)</th>
                                            <?php foreach ($subkriteria as $sk): ?>
                                            <th width="5%" class="text-center bg-info">
                                                <small><strong><?= $sk['kode'] ?></strong></small><br>
                                                <small class="text-muted"><?= $sk['kriteria_nama'] ?></small>
                                            </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subkriteria as $i => $skDari): ?>
                                        <tr>
                                            <td class="text-center align-middle bg-light">
                                                <strong><?= $i + 1 ?></strong>
                                            </td>
                                            <td class="bg-light">
                                                <div class="d-flex flex-column">
                                                    <span class="badge badge-primary mb-1"><?= $skDari['kode'] ?></span>
                                                    <small class="text-muted"><?= $skDari['nama'] ?></small>
                                                    <small><em><?= $skDari['kriteria_nama'] ?></em></small>
                                                </div>
                                            </td>
                                            <?php foreach ($subkriteria as $j => $skKe): ?>
                                            <?php 
                                            $nilai = $matriks_interdependensi[$i][$j] ?? 0;
                                            $formattedNilai = number_format($nilai, 4);
                                            
                                            // Tentukan warna berdasarkan nilai
                                            $bgClass = '';
                                            $textClass = '';
                                            if ($i == $j) {
                                                $bgClass = 'bg-light'; // Diagonal utama
                                                $textClass = 'text-dark';
                                            } elseif ($nilai > 1) {
                                                $bgClass = 'bg-success-light'; // Lebih penting
                                                $textClass = 'text-success';
                                            } elseif ($nilai < 1 && $nilai > 0) {
                                                $bgClass = 'bg-warning-light'; // Kurang penting
                                                $textClass = 'text-warning';
                                            } elseif ($nilai == 0) {
                                                $bgClass = 'bg-danger-light'; // Belum diisi
                                                $textClass = 'text-danger';
                                            }
                                            ?>
                                            <td class="text-center align-middle <?= $bgClass ?> <?= $textClass ?>"
                                                title="<?= $skDari['kode'] ?> → <?= $skKe['kode'] ?>: <?= $formattedNilai ?>"
                                                data-toggle="tooltip">
                                                <div class="d-flex flex-column">
                                                    <span class="font-weight-bold"><?= $formattedNilai ?></span>
                                                    <?php if ($i != $j && $nilai > 0): ?>
                                                    <small class="text-muted">
                                                        <?php if ($nilai >= 1): ?>
                                                            <?= $skDari['kode'] ?> > <?= $skKe['kode'] ?>
                                                        <?php else: ?>
                                                            <?= $skDari['kode'] ?> < <?= $skKe['kode'] ?>
                                                        <?php endif; ?>
                                                    </small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="<?= count($subkriteria) + 2 ?>" class="text-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-square text-success"></i> Lebih penting (nilai > 1) | 
                                                    <i class="fas fa-square text-warning"></i> Kurang penting (0 < nilai < 1) | 
                                                    <i class="fas fa-square text-danger"></i> Belum diisi (nilai = 0) | 
                                                    <i class="fas fa-square text-dark"></i> Diagonal utama (nilai = 1)
                                                </small>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <!-- Statistik Matriks -->
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-info"><i class="fas fa-calculator"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Sel</span>
                                            <span class="info-box-number"><?= count($subkriteria) * count($subkriteria) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Sudah Diisi</span>
                                            <?php 
                                            $filled = 0;
                                            foreach ($matriks_interdependensi as $row) {
                                                foreach ($row as $val) {
                                                    if ($val != 0) $filled++;
                                                }
                                            }
                                            ?>
                                            <span class="info-box-number"><?= $filled ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Belum Diisi</span>
                                            <span class="info-box-number"><?= (count($subkriteria) * count($subkriteria)) - $filled ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-percentage"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Kelengkapan</span>
                                            <span class="info-box-number">
                                                <?= number_format(($filled / (count($subkriteria) * count($subkriteria))) * 100, 1) ?>%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">
                                <i class="icon fas fa-info-circle"></i> Matriks interdependensi akan muncul setelah input pairwise.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">Statistik Matriks</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p>Total Subkriteria: <span class="badge badge-info"><?= count($subkriteria) ?></span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>Total Sel: <span class="badge badge-primary"><?= count($subkriteria) * count($subkriteria) ?></span></p>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <p>Sel Diagonal: <span class="badge badge-secondary"><?= count($subkriteria) ?></span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>Sel Input: <span class="badge badge-warning"><?= (count($subkriteria) * count($subkriteria)) - count($subkriteria) ?></span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>
        </div>
    </div>
    
    <script>
    const subkriteriaData = <?= json_encode($subkriteria) ?>;
    const clustersData = <?= json_encode($clusters ?? []) ?>;
    const historiPairwise = <?= json_encode($histori_pairwise ?? []) ?>;
    
    // Group subkriteria by cluster
    const subkriteriaByCluster = {};
    subkriteriaData.forEach(sk => {
        const clusterId = sk.kriteria_id;
        if (!subkriteriaByCluster[clusterId]) {
            subkriteriaByCluster[clusterId] = [];
        }
        subkriteriaByCluster[clusterId].push(sk);
    });
    
    // Hitung progress pairwise
    function updateProgress() {
        const totalPairs = subkriteriaData.length * (subkriteriaData.length - 1) / 2; // Hanya upper triangle
        const filledPairs = historiPairwise.length;
        const percentage = totalPairs > 0 ? (filledPairs / totalPairs) * 100 : 0;
        
        document.getElementById('filled-count').textContent = filledPairs;
        document.getElementById('total-count').textContent = totalPairs;
        document.getElementById('progress-bar').style.width = percentage + '%';
        document.getElementById('completion-percentage').textContent = percentage.toFixed(1);
        document.getElementById('completion-bar').style.width = percentage + '%';
        
        // Update warna progress bar berdasarkan persentase
        const progressBar = document.getElementById('progress-bar');
        const completionBar = document.getElementById('completion-bar');
        
        if (percentage >= 70) {
            progressBar.className = 'progress-bar bg-success';
            completionBar.className = 'progress-bar bg-success';
        } else if (percentage >= 50) {
            progressBar.className = 'progress-bar bg-warning';
            completionBar.className = 'progress-bar bg-warning';
        } else {
            progressBar.className = 'progress-bar bg-danger';
            completionBar.className = 'progress-bar bg-danger';
        }
    }
    
    // Auto fill kosong dengan AJAX
    document.getElementById('auto-fill-btn').addEventListener('click', function() {
        if (confirm('Auto fill semua pairwise yang belum diisi dengan nilai 1?\n\nIni akan mempercepat proses pengisian.\n\nLanjutkan?')) {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
            
            fetch('<?= base_url("tpp/anp/auto-fill-pairwise") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Auto Fill Berhasil!',
                        html: `<strong>${data.message}</strong><br><br>
                               <ul class="text-left">
                                 <li>Total pairwise ditambahkan: <strong>${data.added_count}</strong></li>
                                 <li>Semua pairwise yang belum diisi sekarang bernilai 1</li>
                                 <li>Matriks sekarang lengkap dan siap untuk perhitungan ANP</li>
                               </ul>`,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Auto Fill Gagal',
                        text: data.message || 'Terjadi kesalahan saat auto fill.',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
                console.error('Error:', error);
            });
        }
    });
    
    // Hitung ANP
    document.getElementById('hitung-anp-btn').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        btn.disabled = true;
        
        fetch('<?= base_url("tpp/anp/hitung-anp") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
        })
        .then(response => {
            // Cek apakah response adalah redirect (status 303)
            if (response.status === 303) {
                // Jika redirect, redirect ke location header
                const redirectUrl = response.headers.get('Location') || '<?= base_url("tpp/anp") ?>';
                window.location.href = redirectUrl;
                return;
            }
            return response.json();
        })
        .then(data => {
            // Jika data undefined (karena redirect), jangan lanjutkan
            if (!data) return;
            
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Perhitungan ANP Berhasil!',
                    html: `<strong>${data.message}</strong><br><br>
                           <ul class="text-left">
                             <li>Perhitungan ANP telah selesai</li>
                             <li>Data interdependensi telah disimpan</li>
                             <li>Silakan cek halaman Hasil ANP untuk melihat detail</li>
                           </ul>`,
                    confirmButtonText: 'Lihat Hasil ANP',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        const redirectUrl = data.redirect_url || '<?= base_url("tpp/anp") ?>';
                        window.location.href = redirectUrl;
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Perhitungan ANP Gagal',
                    text: data.message || 'Terjadi kesalahan saat perhitungan.',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                confirmButtonText: 'OK'
            });
            console.error('Error:', error);
        });
    });
    
    // Auto fill semua pairwise dengan AJAX
    document.getElementById('auto-fill-all-btn').addEventListener('click', function() {
        if (confirm('Auto fill SEMUA pairwise dengan nilai 1?\n\nIni akan menghapus semua pairwise yang ada dan membuat semua kombinasi pairwise baru dengan nilai 1.\n\nIni sangat membantu jika Anda ingin langsung melihat hasil ANP tanpa harus mengisi satu per satu.\n\nLanjutkan?')) {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
            
            fetch('<?= base_url("tpp/anp/auto-fill-all-pairwise") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Auto Fill Semua Berhasil!',
                        html: `<strong>${data.message}</strong><br><br>
                               <ul class="text-left">
                                 <li>Total pairwise yang dibuat: <strong>${data.total_pairs}</strong></li>
                                 <li>Semua kombinasi pairwise sekarang bernilai 1</li>
                                 <li>Matriks sekarang lengkap dan siap untuk perhitungan ANP</li>
                                 <li>Anda bisa langsung klik "Hitung ANP" untuk melihat hasilnya!</li>
                               </ul>`,
                        confirmButtonText: 'Hitung ANP Sekarang',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '<?= base_url("tpp/anp") ?>';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Auto Fill Semua Gagal',
                        text: data.message || 'Terjadi kesalahan saat auto fill.',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
                console.error('Error:', error);
            });
        }
    });
    
    // Update nodes dropdown berdasarkan cluster yang dipilih
    function updateNodesFromCluster(type) {
        const clusterSelect = document.getElementById(`cluster_${type}`);
        const nodeSelect = document.getElementById(`node_${type}`);
        const clusterId = clusterSelect.value;
        
        nodeSelect.innerHTML = '<option value="">-- Pilih Node --</option>';
        
        if (clusterId && subkriteriaByCluster[clusterId]) {
            subkriteriaByCluster[clusterId].forEach(sk => {
                const option = document.createElement('option');
                option.value = sk.id;
                option.textContent = `${sk.kode} - ${sk.nama}`;
                nodeSelect.appendChild(option);
            });
        }
        
        updateCalculation();
    }
    
    // Update perhitungan matematika
    function updateCalculation() {
        const nodeDari = document.getElementById('node_dari').value;
        const nodeKe = document.getElementById('node_ke').value;
        const skala = document.getElementById('skala').value;
        const calculationSection = document.getElementById('calculation-section');
        const calculationContent = document.getElementById('calculation-content');
        
        if (!nodeDari || !nodeKe || !skala) {
            calculationSection.style.display = 'none';
            return;
        }
        
        // Cari data node
        const nodeDariData = subkriteriaData.find(sk => sk.id == nodeDari);
        const nodeKeData = subkriteriaData.find(sk => sk.id == nodeKe);
        
        if (!nodeDariData || !nodeKeData) {
            calculationSection.style.display = 'none';
            return;
        }
        
        const skalaNum = parseFloat(skala);
        const reciprocal = 1 / skalaNum;
        
        // Buat konten perhitungan dengan tampilan yang lebih baik
        calculationContent.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">Informasi Pairwise</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Node Dari:</strong><br>
                            <span class="badge badge-primary">${nodeDariData.kode}</span> ${nodeDariData.nama}<br>
                            <small class="text-muted">Cluster: ${nodeDariData.kriteria_nama}</small></p>
                            
                            <p><strong>Node Ke:</strong><br>
                            <span class="badge badge-info">${nodeKeData.kode}</span> ${nodeKeData.nama}<br>
                            <small class="text-muted">Cluster: ${nodeKeData.kriteria_nama}</small></p>
                            
                            <p><strong>Skala Saaty:</strong><br>
                            <span class="badge badge-${skalaNum == 1 ? 'secondary' : skalaNum <= 3 ? 'success' : skalaNum <= 5 ? 'warning' : 'danger'}">
                                ${skalaNum} - ${getSkalaDescription(skalaNum)}
                            </span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">Rumus Perhitungan</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Nilai a<sub>ij</sub>:</strong> ${skalaNum}</p>
                            <p><strong>Nilai a<sub>ji</sub>:</strong> 1 / ${skalaNum} = ${reciprocal.toFixed(4)}</p>
                            <p><strong>Rumus:</strong> a<sub>ji</sub> = 1 / a<sub>ij</sub></p>
                            <div class="alert alert-light">
                                <small><i class="fas fa-info-circle"></i> Nilai diagonal selalu 1 (self-comparison)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Matriks Interdependensi 2×2</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table calculation-matrix">
                                    <thead>
                                        <tr>
                                            <th width="30%"></th>
                                            <th width="35%" class="text-center bg-info-light">
                                                <span class="badge badge-primary">${nodeDariData.kode}</span><br>
                                                <small>${nodeDariData.nama}</small>
                                            </th>
                                            <th width="35%" class="text-center bg-info-light">
                                                <span class="badge badge-info">${nodeKeData.kode}</span><br>
                                                <small>${nodeKeData.nama}</small>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="bg-light font-weight-bold">
                                                <span class="badge badge-primary">${nodeDariData.kode}</span><br>
                                                <small>${nodeDariData.nama}</small>
                                            </td>
                                            <td class="matrix-diagonal text-center">
                                                <strong>1</strong><br>
                                                <small class="text-muted">self</small>
                                            </td>
                                            <td class="matrix-value text-center">
                                                <strong>${skalaNum.toFixed(2)}</strong><br>
                                                <small class="text-muted">a<sub>ij</sub></small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="bg-light font-weight-bold">
                                                <span class="badge badge-info">${nodeKeData.kode}</span><br>
                                                <small>${nodeKeData.nama}</small>
                                            </td>
                                            <td class="matrix-reciprocal text-center">
                                                <strong>${reciprocal.toFixed(4)}</strong><br>
                                                <small class="text-muted">a<sub>ji</sub></small>
                                            </td>
                                            <td class="matrix-diagonal text-center">
                                                <strong>1</strong><br>
                                                <small class="text-muted">self</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <h6><i class="fas fa-info-circle"></i> Interpretasi Matriks:</h6>
                                <ul class="mb-0">
                                    <li><strong>Diagonal (warna abu-abu):</strong> Nilai = 1 (perbandingan node dengan dirinya sendiri)</li>
                                    <li><strong>Sel a<sub>ij</sub> (warna hijau):</strong> ${nodeDariData.kode} ${skalaNum > 1 ? 'lebih penting' : 'sama penting'} dari ${nodeKeData.kode}</li>
                                    <li><strong>Sel a<sub>ji</sub> (warna kuning):</strong> ${nodeKeData.kode} ${reciprocal > 1 ? 'lebih penting' : 'sama penting'} dari ${nodeDariData.kode}</li>
                                    <li><strong>Konsistensi:</strong> Matriks akan konsisten jika Consistency Ratio (CR) ≤ 0.1</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        calculationSection.style.display = 'block';
    }
    
    // Fungsi untuk mendapatkan deskripsi skala
    function getSkalaDescription(skala) {
        const descriptions = {
            1: 'Sama pentingnya',
            2: 'Antara sama dan sedikit lebih penting',
            3: 'Sedikit lebih penting',
            4: 'Antara sedikit dan lebih penting',
            5: 'Lebih penting',
            6: 'Antara lebih dan sangat lebih penting',
            7: 'Sangat lebih penting',
            8: 'Antara sangat dan mutlak lebih penting',
            9: 'Mutlak lebih penting'
        };
        return descriptions[skala] || 'Tidak diketahui';
    }
    
    // Auto fill matriks dengan nilai default 1
    function autoFillMatrix() {
        if (confirm('Auto fill matriks dengan nilai default 1 untuk semua pairwise?\n\nIni akan mengisi semua pairwise yang belum diisi dengan nilai 1 (1=1).\n\nDiagonal utama sudah otomatis 1.\n\nLanjutkan?')) {
            // Tampilkan loading
            const originalText = document.querySelector('button[onclick="autoFillMatrix()"]').innerHTML;
            document.querySelector('button[onclick="autoFillMatrix()"]').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            document.querySelector('button[onclick="autoFillMatrix()"]').disabled = true;
            
            // Kirim request ke server
            fetch('<?= base_url("tpp/anp/auto-fill-pairwise") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                // Reset button
                document.querySelector('button[onclick="autoFillMatrix()"]').innerHTML = originalText;
                document.querySelector('button[onclick="autoFillMatrix()"]').disabled = false;
                
                if (data.success) {
                    // Tampilkan alert sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Auto Fill Berhasil!',
                        html: `<strong>${data.message}</strong><br><br>
                               <ul class="text-left">
                                 <li>Total pairwise ditambahkan: <strong>${data.added_count}</strong></li>
                                 <li>Semua pairwise yang belum diisi sekarang bernilai 1</li>
                                 <li>Matriks sekarang lengkap dan siap untuk perhitungan ANP</li>
                               </ul>`,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Reload halaman untuk update data
                            window.location.reload();
                        }
                    });
                } else {
                    // Tampilkan alert error
                    Swal.fire({
                        icon: 'error',
                        title: 'Auto Fill Gagal',
                        text: data.message || 'Terjadi kesalahan saat auto fill.',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                // Reset button
                document.querySelector('button[onclick="autoFillMatrix()"]').innerHTML = originalText;
                document.querySelector('button[onclick="autoFillMatrix()"]').disabled = false;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
                console.error('Error:', error);
            });
        }
    }
    
    // Inisialisasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Update nodes jika cluster sudah dipilih
        const clusterDari = document.getElementById('cluster_dari');
        const clusterKe = document.getElementById('cluster_ke');
        
        if (clusterDari.value) updateNodesFromCluster('dari');
        if (clusterKe.value) updateNodesFromCluster('ke');
        
        // Update perhitungan jika data sudah ada
        updateCalculation();
        
        // Update progress bar
        updateProgress();
    });
    </script>
<?= $this->endSection() ?>