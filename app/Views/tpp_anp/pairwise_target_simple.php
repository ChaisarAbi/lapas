<?= $this->extend('layouts/dashboard_template') ?>

<style>
/* Matrix cell styling */
.matrix-cell {
    min-width: 60px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-weight: 500;
}
.matrix-cell.diagonal {
    background-color: #f8f9fa;
    color: #495057;
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
        <a href="<?= base_url('tpp/anp/pairwise-target') ?>" class="nav-link active">
            <i class="nav-icon fas fa-project-diagram"></i>
            <p>Pairwise Comparison (Target-First)</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/anp/pairwise-comparison') ?>" class="nav-link">
            <i class="nav-icon fas fa-exchange-alt"></i>
            <p>Pairwise Comparison (Legacy)</p>
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
    <li class="breadcrumb-item active">Pairwise Comparison ANP (Target-First)</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pairwise Comparison ANP (Target-First Approach)</h3>
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
                        <h5><i class="fas fa-info-circle"></i> Tentang Pairwise Comparison Target-First</h5>
                        <p>Metode ini memungkinkan Anda untuk memilih <strong>target node</strong> terlebih dahulu, kemudian mengisi pairwise comparison untuk influencer nodes yang mempengaruhi target tersebut.</p>
                        <p><strong>Alur kerja:</strong></p>
                        <ol>
                            <li>Pilih target node (yang dipengaruhi)</li>
                            <li>Tentukan influencer nodes (yang mempengaruhi) dengan menambahkan edges/panah</li>
                            <li>Isi pairwise comparison antar influencer nodes (skala 1-9)</li>
                            <li>Ulangi untuk target node lainnya</li>
                        </ol>
                        <p><strong>Skala Saaty (1-9):</strong></p>
                        <ul>
                            <li>1: Sama pentingnya</li>
                            <li>3: Sedikit lebih penting</li>
                            <li>5: Lebih penting</li>
                            <li>7: Sangat lebih penting</li>
                            <li>9: Mutlak lebih penting</li>
                        </ul>
                    </div>
                    
                    <!-- Section 1: Pilih Target Node -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">1. Pilih Target Node (Yang Dipengaruhi)</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($targets)): ?>
                                <div class="alert alert-warning">
                                    <i class="icon fas fa-exclamation-triangle"></i> 
                                    Belum ada target nodes. Silakan tambahkan edges/panah terlebih dahulu untuk menentukan hubungan antar nodes.
                                </div>
                            <?php else: ?>
                                <form method="get" action="<?= base_url('tpp/anp/pairwise-target') ?>" id="target-form">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="target_id">Pilih Target Node:</label>
                                                <select name="target_id" id="target_id" class="form-control" onchange="this.form.submit()">
                                                    <option value="">-- Pilih Target Node --</option>
                                                    <?php foreach ($targets as $target): ?>
                                                    <option value="<?= $target['id'] ?>" 
                                                            <?= $selected_target && $selected_target['id'] == $target['id'] ? 'selected' : '' ?>>
                                                        <?= $target['kode'] ?> - <?= $target['nama'] ?> 
                                                        (<?= $target['kriteria_nama'] ?>)
                                                        - <?= number_format($target['progress_percentage'], 1) ?>% 
                                                        (<?= $target['filled_pairs'] ?>/<?= $target['total_pairs'] ?>)
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fas fa-check"></i> Pilih Target
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                                <?php if ($selected_target): ?>
                                <div class="alert alert-info mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">
                                                <span class="badge badge-primary"><?= $selected_target['kode'] ?></span>
                                                <?= $selected_target['nama'] ?>
                                            </h5>
                                            <p class="mb-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-layer-group"></i> <?= $selected_target['kriteria_nama'] ?>
                                                </small>
                                            </p>
                                            <p class="mb-1">
                                                <small>
                                                    <i class="fas fa-arrow-right"></i> <?= $selected_target['influencer_count'] ?> influencer nodes
                                                </small>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <div class="progress-group">
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-<?= $selected_target['progress_percentage'] >= 70 ? 'success' : ($selected_target['progress_percentage'] >= 30 ? 'warning' : 'danger') ?>"
                                                         style="width: <?= min($selected_target['progress_percentage'], 100) ?>%"></div>
                                                </div>
                                                <small><?= number_format($selected_target['progress_percentage'], 1) ?>%</small>
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge badge-info">
                                                    <?= $selected_target['filled_pairs'] ?>/<?= $selected_target['total_pairs'] ?> pairwise
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($selected_target): ?>
                        <!-- Section 2: Input Pairwise untuk Target yang Dipilih -->
                        <div class="card card-success mt-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    2. Input Pairwise Comparison untuk Target: 
                                    <span class="badge badge-primary"><?= $selected_target['kode'] ?></span> <?= $selected_target['nama'] ?>
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-warning btn-sm" id="auto-fill-target-btn">
                                        <i class="fas fa-bolt"></i> Auto Fill Kosong
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (empty($matrix_data['influencers'])): ?>
                                    <div class="alert alert-warning">
                                        <i class="icon fas fa-exclamation-triangle"></i> 
                                        Tidak ada influencer nodes untuk target ini. Silakan tambahkan edges/panah terlebih dahulu.
                                    </div>
                                <?php else: ?>
                                    <!-- Progress untuk target ini -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="progress-group">
                                                <span class="progress-text">Pairwise Terisi</span>
                                                <span class="float-right">
                                                    <b><?= $matrix_data['filled_pairs'] ?></b> / <span><?= $matrix_data['total_pairs'] ?></span>
                                                </span>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-<?= $matrix_data['progress_percentage'] >= 70 ? 'success' : ($matrix_data['progress_percentage'] >= 30 ? 'warning' : 'danger') ?>"
                                                         style="width: <?= min($matrix_data['progress_percentage'], 100) ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="progress-group">
                                                <span class="progress-text">Tingkat Kelengkapan</span>
                                                <span class="float-right">
                                                    <b><?= number_format($matrix_data['progress_percentage'], 1) ?></b>%
                                                </span>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-info" style="width: <?= min($matrix_data['progress_percentage'], 100) ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Form Input Pairwise -->
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">Input Pairwise Comparison</h3>
                                        </div>
                                        <div class="card-body">
                                            <form action="<?= base_url('tpp/anp/simpan-pairwise-target') ?>" method="post" id="pairwise-form">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="target_id" value="<?= $selected_target['id'] ?>">
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="node_dari">Node Dari (Influencer):</label>
                                                            <select name="node_dari" id="node_dari" class="form-control" required>
                                                                <option value="">-- Pilih Node Dari --</option>
                                                                <?php foreach ($matrix_data['influencers'] as $influencer): ?>
                                                                <option value="<?= $influencer['id'] ?>">
                                                                    <?= $influencer['kode'] ?> - <?= $influencer['nama'] ?> (<?= $influencer['kriteria_nama'] ?>)
                                                                </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="node_ke">Node Ke (Influencer):</label>
                                                            <select name="node_ke" id="node_ke" class="form-control" required>
                                                                <option value="">-- Pilih Node Ke --</option>
                                                                <?php foreach ($matrix_data['influencers'] as $influencer): ?>
                                                                <option value="<?= $influencer['id'] ?>">
                                                                    <?= $influencer['kode'] ?> - <?= $influencer['nama'] ?> (<?= $influencer['kriteria_nama'] ?>)
                                                                </option>
                                                                <?php endforeach; ?>
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
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Keterangan:</label>
                                                            <div id="calculation-info" class="alert alert-light">
                                                                <p id="calculation-text">Pilih skala untuk melihat perhitungan</p>
                                                                <p id="reciprocal-text" class="mb-0" style="display: none;">
                                                                    <strong>Nilai kebalikan:</strong> <span id="reciprocal-value"></span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-save"></i> Simpan Pairwise
                                                        </button>
                                                        <button type="reset" class="btn btn-secondary">
                                                            <i class="fas fa-redo"></i> Reset
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <!-- Container untuk tabel hasil -->
                                    <div id="result-tables">
                                        <?php if ($matrix_data['is_complete']): ?>
                                            <?= view('tpp_anp/_result_tables', ['matrix_data' => $matrix_data, 'ahp_report' => $ahp_report ?? null]) ?>
                                        <?php else: ?>
                                            <div class="alert alert-warning">
                                                <i class="icon fas fa-exclamation-triangle"></i> 
                                                <strong>Matriks belum lengkap!</strong> 
                                                Lengkapi pairwise terlebih dahulu (<?= $matrix_data['filled_pairs'] ?>/<?= $matrix_data['total_pairs'] ?>).
                                                Tabel akan muncul otomatis setelah semua pairwise terisi.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Fungsi untuk update perhitungan
function updateCalculation() {
    const skala = document.getElementById('skala').value;
    const calculationText = document.getElementById