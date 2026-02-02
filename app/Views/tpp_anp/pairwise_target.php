<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
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
</head>
<body>
    <?= $this->include('layouts/dashboard_template') ?>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-diagram-3 me-2"></i>
                            Pairwise Comparison ANP (Target-First)
                        </h4>
                        <?php if ($periode && isset($periode['nama'])): ?>
                            <span class="badge bg-primary">Periode: <?= esc($periode['nama']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <!-- Flash Messages -->
                        <?php if (session()->has('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?= session('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->has('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?= session('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Target Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card target-card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="bi bi-target me-2"></i>
                                            Pilih Target Node
                                        </h5>
                                        <p class="card-text text-muted">
                                            Pilih subkriteria yang akan menjadi target dalam pairwise comparison.
                                        </p>
                                        <div class="btn-group-vertical w-100" role="group">
                                            <?php foreach ($targets as $target): ?>
                                                <button type="button" 
                                                        class="btn btn-outline-primary text-start <?= $selected_target && $selected_target['id'] == $target['id'] ? 'active' : '' ?>"
                                                        onclick="window.location.href='<?= base_url('tpp/anp/pairwise-target?target_id=' . $target['id']) ?>'">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong><?= esc($target['kode']) ?></strong>
                                                            <br><small class="text-muted"><?= esc($target['nama']) ?></small>
                                                        </div>
                                                        <div>
                                                            <?php if ($selected_target && $selected_target['id'] == $target['id']): ?>
                                                                <span class="badge bg-success">Dipilih</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-light text-dark">Pilih</span>
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
                                                <i class="bi bi-info-circle me-2"></i>
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
                                                <i class="bi bi-plus-circle me-2"></i>
                                                Input Pairwise Comparison untuk Target: 
                                                <span class="badge bg-primary"><?= esc($selected_target['kode']) ?> - <?= esc($selected_target['nama']) ?></span>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="<?= base_url('tpp/anp/simpan-pairwise-target') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="target_id" value="<?= $selected_target['id'] ?>">
                                                
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Node Dari</label>
                                                        <select class="form-select" name="node_dari" required>
                                                            <option value="">Pilih node dari...</option>
                                                            <?php foreach ($matrix_data['influencers'] ?? [] as $influencer): ?>
                                                                <option value="<?= $influencer['id'] ?>">
                                                                    <?= esc($influencer['kode']) ?> - <?= esc($influencer['nama']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Node Ke</label>
                                                        <select class="form-select" name="node_ke" required>
                                                            <option value="">Pilih node ke...</option>
                                                            <?php foreach ($matrix_data['influencers'] ?? [] as $influencer): ?>
                                                                <option value="<?= $influencer['id'] ?>">
                                                                    <?= esc($influencer['kode']) ?> - <?= esc($influencer['nama']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Skala (1-9)</label>
                                                        <input type="number" class="form-control" name="skala" min="1" max="9" step="0.1" required
                                                               placeholder="Contoh: 3.0">
                                                        <div class="form-text">Skala 1-9 sesuai metode AHP</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="bi bi-save me-2"></i>
                                                            Simpan Pairwise
                                                        </button>
                                                        <button type="button" class="btn btn-warning" onclick="autoFillTarget()">
                                                            <i class="bi bi-magic me-2"></i>
                                                            Auto Fill Target
                                                        </button>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <button type="button" class="btn btn-success" onclick="hitungAnpTargetFirst()">
                                                            <i class="bi bi-calculator me-2"></i>
                                                            Hitung ANP Target-First
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Matrix Display -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">
                                                <i class="bi bi-table me-2"></i>
                                                Matriks Pairwise Comparison
                                            </h5>
                                            <div>
                                                <?php if ($matrix_data && $matrix_data['is_complete']): ?>
                                                    <span class="status-badge complete-badge">
                                                        <i class="bi bi-check-circle-fill me-1"></i>
                                                        Lengkap (<?= $matrix_data['filled_pairs'] ?>/<?= $matrix_data['total_pairs'] ?>)
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-badge incomplete-badge">
                                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                        Belum Lengkap (<?= $matrix_data['filled_pairs'] ?? 0 ?>/<?= $matrix_data['total_pairs'] ?? 0 ?>)
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($matrix_data && !empty($matrix_data['influencers'])): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th class="matrix-header">Node</th>
                                                                <?php foreach ($matrix_data['influencers'] as $influencer): ?>
                                                                    <th class="matrix-header" title="<?= esc($influencer['nama']) ?>">
                                                                        <?= esc($influencer['kode']) ?>
                                                                    </th>
                                                                <?php endforeach; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($matrix_data['influencers'] as $i => $influencer): ?>
                                                                <tr>
                                                                    <td class="matrix-header" title="<?= esc($influencer['nama']) ?>">
                                                                        <?= esc($influencer['kode']) ?>
                                                                    </td>
                                                                    <?php foreach ($matrix_data['influencers'] as $j => $inf): ?>
                                                                        <td class="matrix-cell <?= $i == $j ? 'bg-light' : '' ?>">
                                                                            <?php if ($i == $j): ?>
                                                                                <span class="text-muted">1.0</span>
                                                                            <?php else: ?>
                                                                                <?php if ($matrix_data['matrix'][$i][$j] > 0): ?>
                                                                                    <span class="badge bg-primary"><?= number_format($matrix_data['matrix'][$i][$j], 2) ?></span>
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
                                            <?php else: ?>
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    Belum ada influencer untuk target ini atau data tidak ditemukan.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- AHP Report -->
                            <?php if (isset($ahpReport) && $ahpReport): ?>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0">
                                                    <i class="bi bi-file-text me-2"></i>
                                                    Laporan AHP untuk Target: <?= esc($selected_target['kode']) ?>
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Konsistensi</h6>
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item">
                                                                <strong>λ_max:</strong> <?= number_format($ahpReport['lambda_max'], 4) ?>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <strong>CI:</strong> <?= number_format($ahpReport['ci'], 4) ?>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <strong>RI:</strong> <?= number_format($ahpReport['ri'], 2) ?>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <strong>CR:</strong> <?= number_format($ahpReport['cr'], 4) ?>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <strong>Konsisten:</strong> 
                                                                <span class="badge <?= $ahpReport['konsisten'] ? 'bg-success' : 'bg-danger' ?>">
                                                                    <?= $ahpReport['konsisten'] ? 'Ya' : 'Tidak' ?>
                                                                </span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Bobot Prioritas</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Node</th>
                                                                        <th>Bobot</th>
                                                                        <th>Presentase</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($ahpReport['weights'] as $idx => $weight): ?>
                                                                        <tr>
                                                                            <td><?= esc($ahpReport['influencers'][$idx]['kode']) ?></td>
                                                                            <td><?= number_format($weight, 4) ?></td>
                                                                            <td><?= number_format($weight * 100, 2) ?>%</td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
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
                                        <i class="bi bi-info-circle me-2"></i>
                                        Silakan pilih target node terlebih dahulu untuk melihat pairwise comparison.
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function autoFillTarget() {
            if (confirm('Apakah Anda yakin ingin melakukan auto fill untuk target ini?')) {
                window.location.href = '<?= base_url('tpp/anp/auto-fill-pairwise-target') ?>?target_id=<?= $selected_target['id'] ?? '' ?>';
            }
        }

        function hitungAnpTargetFirst() {
            if (confirm('Apakah Anda yakin ingin menghitung ANP dengan pendekatan target-first?')) {
                window.location.href = '<?= base_url('tpp/anp/hitung-anp-target-first') ?>';
            }
        }
    </script>
</body>
</html>