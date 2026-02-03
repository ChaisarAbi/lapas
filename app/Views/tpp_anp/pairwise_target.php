<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'anp_pairwise';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/anp') ?>">ANP</a></li>
    <li class="breadcrumb-item active">Pairwise Comparison</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <style>
        .matrix-cell {
            width: 80px;
            height: 80px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .matrix-header {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        .progress-container {
            margin-bottom: 20px;
        }
        .target-card {
            border-left: 4px solid #007bff;
        }
        .influencer-card {
            border-left: 4px solid #28a745;
        }
        .btn-group-vertical .btn {
            text-align: left;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 2px 8px;
            border-radius: 12px;
        }
        .complete-badge {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .incomplete-badge {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-balance-scale mr-2"></i>
                        Pairwise Comparison ANP (Target-First)
                    </h3>
                    <div class="card-tools">
                        <?php if ($periode && isset($periode['nama'])): ?>
                            <span class="badge badge-primary mr-2">Periode: <?= esc($periode['nama']) ?></span>
                        <?php endif; ?>
                        <a href="<?= base_url('/tpp/anp/edges') ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-arrow-right-circle mr-1"></i> Kelola Edges
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check"></i> <?= $success_message ?>
                        </div>
                    <?php elseif (session()->getFlashdata('success')): ?>
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

                    <!-- Hasil Perhitungan ANP -->
                    <?php if (isset($hasil_anp)): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Hasil Perhitungan ANP (Target-First)</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4>Konsistensi Global</h4>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>λ_max:</th>
                                                        <td><?= number_format($hasil_anp['lambda_max'], 4) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>CI:</th>
                                                        <td><?= number_format($hasil_anp['ci'], 4) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>RI:</th>
                                                        <td><?= number_format($hasil_anp['ri'], 4) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>CR:</th>
                                                        <td><?= number_format($hasil_anp['cr'], 4) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Konsisten:</th>
                                                        <td>
                                                            <?php if ($hasil_anp['konsisten']): ?>
                                                                <span class="badge badge-success">Ya</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger">Tidak</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Bobot Akhir ANP</h4>
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Subkriteria</th>
                                                            <th>Bobot</th>
                                                            <th>%</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($hasil_anp['bobot'] as $index => $item): ?>
                                                            <tr>
                                                                <td><?= esc($item['kode']) ?> - <?= esc($item['nama']) ?></td>
                                                                <td><?= number_format($item['weight'], 6) ?></td>
                                                                <td><?= number_format($item['weight'] * 100, 2) ?>%</td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-sm text-muted">Total: <?= number_format($hasil_anp['total_bobot_akhir'], 4) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Hasil Perhitungan Per Target -->
                    <?php if (isset($calculation_results) && !empty($calculation_results)): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Hasil Perhitungan per Target</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php foreach ($calculation_results as $targetId => $result): ?>
                                                <div class="col-md-6 mb-4">
                                                    <div class="card">
                                                        <div class="card-header bg-info text-white">
                                                            <h6 class="mb-0"><?= esc($result['target']['kode']) ?> - <?= esc($result['target']['nama']) ?></h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <h7>Matriks Node-Node (Pairwise)</h7>
                                                            <div class="table-responsive mb-3">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="matrix-header">Node</th>
                                                                            <?php foreach ($result['influencers'] as $influencer): ?>
                                                                                <th class="matrix-header" title="<?= esc($influencer['nama']) ?>">
                                                                                    <?= esc($influencer['kode']) ?>
                                                                                </th>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($result['influencers'] as $i => $influencer): ?>
                                                                            <tr>
                                                                                <td class="matrix-header" title="<?= esc($influencer['nama']) ?>">
                                                                                    <?= esc($influencer['kode']) ?>
                                                                                </td>
                                                                                <?php foreach ($result['influencers'] as $j => $inf): ?>
                                                                                    <td class="matrix-cell <?= $i == $j ? 'bg-light' : '' ?>">
                                                                                        <?php if ($i == $j): ?>
                                                                                            <span class="text-muted">1.0000</span>
                                                                                        <?php else: ?>
                                                                                            <?php if (isset($result['matrix'][$i][$j]) && $result['matrix'][$i][$j] > 0): ?>
                                                                                                <span class="badge badge-primary"><?= number_format($result['matrix'][$i][$j], 4) ?></span>
                                                                                            <?php else: ?>
                                                                                                <span class="text-muted">-</span>
                                                                                            <?php endif; ?>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                <?php endforeach; ?>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <h7>Consistency Measure</h7>
                                                            <table class="table table-sm">
                                                                <tr>
                                                                    <th>λ_max:</th>
                                                                    <td><?= number_format($result['ahp_report']['lambda_max'], 4) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>CI:</th>
                                                                    <td><?= number_format($result['ahp_report']['ci'], 4) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>RI:</th>
                                                                    <td><?= number_format($result['ahp_report']['ri'], 4) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>CR:</th>
                                                                    <td><?= number_format($result['ahp_report']['cr'], 4) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Konsisten:</th>
                                                                    <td>
                                                                        <?php if ($result['ahp_report']['konsisten']): ?>
                                                                            <span class="badge badge-success">Ya</span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-danger">Tidak</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            
                                                            <h7>Bobot Prioritas Influencer</h7>
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Node</th>
                                                                        <th>Bobot</th>
                                                                        <th>%</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($result['ahp_report']['weights'] as $idx => $weight): ?>
                                                                        <tr>
                                                                            <td><?= esc($result['influencers'][$idx]['kode']) ?></td>
                                                                            <td><?= number_format($weight, 6) ?></td>
                                                                            <td><?= number_format($weight * 100, 2) ?>%</td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Target Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card target-card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-bullseye mr-2"></i>
                                        Pilih Target Node
                                    </h5>
                                    <p class="card-text text-muted">
                                        Pilih subkriteria yang akan menjadi target dalam pairwise comparison.
                                    </p>
                                    <div class="btn-group-vertical w-100" role="group">
                                        <?php foreach ($targets as $target): ?>
                                            <button type="button" 
                                                    class="btn btn-outline-primary text-left <?= $selected_target && $selected_target['id'] == $target['id'] ? 'active' : '' ?>"
                                                    onclick="window.location.href='<?= base_url('tpp/anp/pairwise-target?target_id=' . $target['id']) ?>'">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?= esc($target['kode']) ?></strong>
                                                        <br><small class="text-muted"><?= esc($target['nama']) ?></small>
                                                    </div>
                                                    <div>
                                                        <?php if ($selected_target && $selected_target['id'] == $target['id']): ?>
                                                            <span class="badge badge-success">Dipilih</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-light text-dark">Pilih</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Target Information -->
                        <?php if ($selected_target): ?>
                            <div class="col-md-6">
                                <div class="card influencer-card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Informasi Target
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Kode:</strong> <?= esc($selected_target['kode']) ?></p>
                                                <p><strong>Nama:</strong> <?= esc($selected_target['nama']) ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Kriteria:</strong> <?= esc($selected_target['kriteria_nama']) ?></p>
                                                <p><strong>ID:</strong> <?= esc($selected_target['id']) ?></p>
                                            </div>
                                        </div>
                                        
                                        <?php if ($matrix_data): ?>
                                            <div class="progress-container">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Progress Pairwise:</span>
                                                    <span><?= $matrix_data['filled_pairs'] ?> / <?= $matrix_data['total_pairs'] ?> pasangan</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar <?= $matrix_data['is_complete'] ? 'bg-success' : 'bg-warning' ?>" 
                                                         style="width: <?= $matrix_data['progress_percentage'] ?>%">
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    <?= $matrix_data['progress_percentage'] ?>% selesai
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <strong>Informasi Edges:</strong> Target ini dapat dipengaruhi oleh edges dari node lain. 
                                            <a href="<?= base_url('/tpp/anp/edges') ?>">Kelola edges</a> untuk menentukan hubungan interdependensi.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pairwise Input Form -->
                    <?php if ($selected_target): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-plus-circle mr-2"></i>
                                            Input Pairwise Comparison untuk Target: 
                                            <span class="badge badge-primary"><?= esc($selected_target['kode']) ?> - <?= esc($selected_target['nama']) ?></span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?= base_url('tpp/anp/simpan-pairwise-target') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="target_id" value="<?= $selected_target['id'] ?>">
                                            
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label>Node Dari</label>
                                                    <select class="form-control" name="node_dari" required>
                                                        <option value="">Pilih node dari...</option>
                                                        <?php foreach ($matrix_data['influencers'] ?? [] as $influencer): ?>
                                                            <option value="<?= $influencer['id'] ?>">
                                                                <?= esc($influencer['kode']) ?> - <?= esc($influencer['nama']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label>Node Ke (Influencer)</label>
                                                    <select class="form-control" name="node_ke" required>
                                                        <option value="">Pilih influencer node...</option>
                                                        <?php if (isset($matrix_data['influencers']) && !empty($matrix_data['influencers'])): ?>
                                                            <?php foreach ($matrix_data['influencers'] as $influencer): ?>
                                                                <option value="<?= $influencer['id'] ?>">
                                                                    <?= esc($influencer['kode']) ?> - <?= esc($influencer['nama']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <option value="">Tidak ada influencer</option>
                                                        <?php endif; ?>
                                                    </select>
                                                    <div class="form-text text-muted">
                                                        Pilih node yang mempengaruhi target <?= esc($selected_target['kode']) ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label>Skala (1-9)</label>
                                                    <input type="number" class="form-control" name="skala" min="1" max="9" step="0.1" required
                                                           placeholder="Contoh: 3.0">
                                                    <div class="form-text">Skala 1-9 sesuai metode AHP</div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save mr-2"></i>
                                                        Simpan Pairwise
                                                    </button>
                                                    <button type="button" class="btn btn-warning" onclick="autoFillTarget()">
                                                        <i class="fas fa-magic mr-2"></i>
                                                        Auto Fill Target
                                                    </button>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <button type="button" class="btn btn-success" onclick="hitungAnpTargetFirst()">
                                                        <i class="fas fa-calculator mr-2"></i>
                                                        Hitung ANP Target-First
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compact Matrix Display (2x2 atau 3x3) dengan Perhitungan -->
                        <?php if ($matrix_data && !empty($matrix_data['influencers'])): ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">
                                                <i class="fas fa-calculator mr-2"></i>
                                                Matriks dan Perhitungan AHP (Target: <?= esc($selected_target['kode']) ?>)
                                            </h5>
                                            <div>
                                                <?php if ($matrix_data && $matrix_data['is_complete']): ?>
                                                    <span class="status-badge complete-badge">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Lengkap (<?= $matrix_data['filled_pairs'] ?>/<?= $matrix_data['total_pairs'] ?>)
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-badge incomplete-badge">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        Belum Lengkap (<?= $matrix_data['filled_pairs'] ?? 0 ?>/<?= $matrix_data['total_pairs'] ?? 0 ?>)
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!-- Tampilkan matriks kecil (maks 3x3) -->
                                            <?php 
                                            // Ambil maksimal 3 influencer pertama sebagai contoh
                                            $max_display = min(3, count($matrix_data['influencers']));
                                            $display_influencers = array_slice($matrix_data['influencers'], 0, $max_display);
                                            ?>
                                            
                                            <div class="row">
                                                <!-- Matriks kecil -->
                                                <div class="col-md-6">
                                                    <h6>Matriks Pairwise (<?= $max_display ?>x<?= $max_display ?> contoh):</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th class="matrix-header">Node</th>
                                                                    <?php foreach ($display_influencers as $influencer): ?>
                                                                        <th class="matrix-header" title="<?= esc($influencer['nama']) ?>">
                                                                            <?= esc($influencer['kode']) ?>
                                                                        </th>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($display_influencers as $i => $influencer): ?>
                                                                    <tr>
                                                                        <td class="matrix-header" title="<?= esc($influencer['nama']) ?>">
                                                                            <?= esc($influencer['kode']) ?>
                                                                        </td>
                                                                        <?php foreach ($display_influencers as $j => $inf): ?>
                                                                            <td class="matrix-cell <?= $i == $j ? 'bg-light' : '' ?>">
                                                                                <?php if ($i == $j): ?>
                                                                                    <span class="text-muted">1.0000</span>
                                                                                <?php else: ?>
                                                                                    <?php if ($matrix_data['matrix'][$i][$j] > 0): ?>
                                                                                        <span class="badge badge-primary"><?= number_format($matrix_data['matrix'][$i][$j], 4) ?></span>
                                                                                    <?php else: ?>
                                                                                        <span class="text-muted">-</span>
                                                                                    <?php endif; ?>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                        <?php endforeach; ?>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <small class="text-muted">
                                                        Menampilkan <?= $max_display ?> dari <?= count($matrix_data['influencers']) ?> influencer. 
                                                        Semua data tersimpan untuk perhitungan supermatrix.
                                                    </small>
                                                </div>
                                                
                                                <!-- Perhitungan AHP -->
                                                <div class="col-md-6">
                                                    <?php if ($matrix_data['is_complete'] && isset($ahpReport) && $ahpReport): ?>
                                                        <h6>Hasil Perhitungan AHP:</h6>
                                                        
                                                        <div class="alert <?= $ahpReport['konsisten'] ? 'alert-success' : 'alert-warning' ?>">
                                                            <i class="fas <?= $ahpReport['konsisten'] ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-2"></i>
                                                            <strong>Konsistensi:</strong> 
                                                            <?= $ahpReport['konsisten'] ? 'KONSISTEN' : 'TIDAK KONSISTEN' ?>
                                                        </div>
                                                        
                                                        <h6>Ukuran Konsistensi:</h6>
                                                        <ul class="list-group list-group-flush mb-3">
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <span>λ_max (Lambda Maksimum)</span>
                                                                <span class="badge badge-primary badge-pill"><?= number_format($ahpReport['lambda_max'], 4) ?></span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <span>CI (Consistency Index)</span>
                                                                <span class="badge badge-info badge-pill"><?= number_format($ahpReport['ci'], 4) ?></span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <span>RI (Random Index)</span>
                                                                <span class="badge badge-secondary badge-pill"><?= number_format($ahpReport['ri'], 2) ?></span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <span>CR (Consistency Ratio)</span>
                                                                <span class="badge <?= $ahpReport['cr'] <= 0.1 ? 'badge-success' : 'badge-danger' ?> badge-pill">
                                                                    <?= number_format($ahpReport['cr'], 4) ?>
                                                                </span>
                                                            </li>
                                                        </ul>
                                                        
                                                        <h6>Bobot Prioritas (Contoh <?= $max_display ?> pertama):</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Node</th>
                                                                        <th>Bobot</th>
                                                                        <th>%</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php for ($idx = 0; $idx < $max_display; $idx++): ?>
                                                                        <tr>
                                                                            <td><?= esc($ahpReport['influencers'][$idx]['kode']) ?></td>
                                                                            <td><?= number_format($ahpReport['weights'][$idx], 6) ?></td>
                                                                            <td><?= number_format($ahpReport['weights'][$idx] * 100, 2) ?>%</td>
                                                                        </tr>
                                                                    <?php endfor; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <small class="text-muted">
                                                            Total influencer: <?= count($ahpReport['influencers']) ?> nodes. 
                                                            Bobot prioritas tersimpan untuk supermatrix.
                                                        </small>
                                                    <?php elseif (!$matrix_data['is_complete']): ?>
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                                            Matriks belum lengkap. Lengkapi semua pairwise terlebih dahulu untuk menghitung AHP.
                                                            <br><small>Progress: <?= $matrix_data['filled_pairs'] ?? 0 ?> / <?= $matrix_data['total_pairs'] ?? 0 ?> pairs</small>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="alert alert-info">
                                                            <i class="fas fa-info-circle mr-2"></i>
                                                            Klik "Hitung ANP Target-First" untuk menghitung dan menyimpan hasil perhitungan.
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <!-- Informasi Penyimpanan -->
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <div class="alert alert-primary">
                                                        <i class="fas fa-database mr-2"></i>
                                                        <strong>Informasi Penyimpanan:</strong> 
                                                        Hasil perhitungan bobot prioritas akan disimpan ke tabel interdependensi 
                                                        untuk digunakan dalam pembuatan supermatrix. 
                                                        <a href="<?= base_url('tpp/anp') ?>" class="alert-link">Lihat hasil ANP lengkap</a>.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Silakan pilih target node terlebih dahulu untuk melihat pairwise comparison.
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Pairwise comparison menggunakan pendekatan target-first dalam Analytic Network Process (ANP). 
                        Pilih target node terlebih dahulu, lalu lakukan perbandingan antar influencer nodes untuk target tersebut.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function autoFillTarget() {
            if (confirm('Apakah Anda yakin ingin melakukan auto fill untuk target ini?')) {
                window.location.href = '<?= base_url('tpp/anp/auto-fill-pairwise-target') ?>?target_id=<?= $selected_target['id'] ?? '' ?>';
            }
        }

        function hitungAnpTargetFirst() {
            if (confirm('Apakah Anda yakin ingin menghitung ANP dengan pendekatan target-first?')) {
                // Tambahkan parameter target_id ke URL
                var targetId = '<?= $selected_target ? $selected_target['id'] : '' ?>';
                if (targetId) {
                    window.location.href = '<?= base_url('tpp/anp/hitung-anp-target-first') ?>?target_id=' + targetId;
                } else {
                    window.location.href = '<?= base_url('tpp/anp/hitung-anp-target-first') ?>';
                }
            }
        }
    </script>
<?= $this->endSection() ?>
