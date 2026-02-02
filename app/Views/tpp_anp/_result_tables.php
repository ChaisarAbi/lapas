<!-- Partial view for matrix and AHP report -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-table me-2"></i>
                    Matriks Pairwise Comparison
                </h5>
                <div>
                    <?php if ($matrix_data['is_complete']): ?>
                        <span class="status-badge complete-badge">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            Lengkap (<?= $matrix_data['filled_pairs'] ?>/<?= $matrix_data['total_pairs'] ?>)
                        </span>
                    <?php else: ?>
                        <span class="status-badge incomplete-badge">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Belum Lengkap (<?= $matrix_data['filled_pairs'] ?>/<?= $matrix_data['total_pairs'] ?>)
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
<?php if ($ahp_report): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-file-text me-2"></i>
                        Laporan AHP untuk Target Ini
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Konsistensi</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>λ_max:</strong> <?= number_format($ahp_report['lambda_max'], 4) ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>CI:</strong> <?= number_format($ahp_report['ci'], 4) ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>RI:</strong> <?= number_format($ahp_report['ri'], 2) ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>CR:</strong> <?= number_format($ahp_report['cr'], 4) ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Konsisten:</strong> 
                                    <span class="badge <?= $ahp_report['konsisten'] ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $ahp_report['konsisten'] ? 'Ya' : 'Tidak' ?>
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
                                        <?php foreach ($ahp_report['weights'] as $idx => $weight): ?>
                                            <tr>
                                                <td><?= esc($ahp_report['influencers'][$idx]['kode']) ?></td>
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