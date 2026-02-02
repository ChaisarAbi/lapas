<?php if ($matrix_data['is_complete']): ?>
    <!-- Matriks Pairwise untuk Target Ini -->
    <div class="card card-primary mt-4">
        <div class="card-header">
            <h3 class="card-title">Matriks Pairwise Comparison</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-info" onclick="refreshMatrix()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered calculation-matrix">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Influencer</th>
                            <?php foreach ($matrix_data['influencers'] as $influencer): ?>
                            <th class="text-center">
                                <small><?= $influencer['kode'] ?></small><br>
                                <span class="badge badge-light"><?= $influencer['nama'] ?></span>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matrix_data['influencers'] as $i => $rowInfluencer): ?>
                        <tr>
                            <td>
                                <strong><?= $rowInfluencer['kode'] ?></strong><br>
                                <small><?= $rowInfluencer['nama'] ?></small>
                            </td>
                            <?php foreach ($matrix_data['influencers'] as $j => $colInfluencer): ?>
                            <td class="text-center">
                                <?php 
                                $value = $matrix_data['matrix'][$i][$j];
                                $isDiagonal = ($i == $j);
                                $isFilled = ($value > 0 && !$isDiagonal);
                                $isReciprocal = false;
                                
                                if (!$isDiagonal && $value > 0) {
                                    // Cek apakah ini reciprocal dari nilai yang sudah ada
                                    $reverseValue = $matrix_data['matrix'][$j][$i];
                                    if ($reverseValue > 0 && abs($value - (1/$reverseValue)) < 0.0001) {
                                        $isReciprocal = true;
                                    }
                                }
                                ?>
                                <div class="matrix-cell 
                                    <?= $isDiagonal ? 'diagonal' : '' ?>
                                    <?= $isFilled ? 'filled' : '' ?>
                                    <?= $isReciprocal ? 'reciprocal' : '' ?>
                                    <?= (!$isDiagonal && $value == 0) ? 'empty' : '' ?>">
                                    <?php if ($isDiagonal): ?>
                                        1.000
                                    <?php elseif ($value > 0): ?>
                                        <?= number_format($value, 3) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="matrix-cell diagonal mr-2"></div>
                            <span>Diagonal (self-comparison)</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="matrix-cell filled mr-2"></div>
                            <span>Pairwise terisi</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="matrix-cell reciprocal mr-2"></div>
                            <span>Nilai kebalikan (auto)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabel Hasil & Perhitungan AHP -->
    <?php if (isset($ahp_report) && $ahp_report): ?>
    <div class="card card-success mt-4">
        <div class="card-header">
            <h3 class="card-title">Tabel Normalisasi & Prioritas</h3>
            <div class="card-tools">
                <span class="badge badge-<?= $ahp_report['konsisten'] ? 'success' : 'danger' ?>">
                    <?= $ahp_report['konsisten'] ? 'Konsisten' : 'Tidak Konsisten' ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <!-- Tabel Normalisasi & Prioritas -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 100px;">Kode</th>
                            <?php foreach ($ahp_report['influencers'] as $influencer): ?>
                            <th class="text-center" style="min-width: 80px;">
                                <small><?= $influencer['kode'] ?></small>
                            </th>
                            <?php endforeach; ?>
                            <th class="text-center" style="min-width: 100px;">Prioritas</th>
                            <th class="text-center" style="min-width: 120px;">Consistency Measure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < $ahp_report['k']; $i++): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= $ahp_report['influencers'][$i]['kode'] ?>
                            </td>
                            <?php for ($j = 0; $j < $ahp_report['k']; $j++): ?>
                            <td class="text-center">
                                <?= number_format($ahp_report['normalized'][$i][$j], 4) ?>
                            </td>
                            <?php endfor; ?>
                            <td class="text-center font-weight-bold text-primary">
                                <?= number_format($ahp_report['weights'][$i], 4) ?>
                            </td>
                            <td class="text-center">
                                <?= number_format($ahp_report['lambda_i'][$i], 4) ?>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Ringkasan Konsistensi -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Ringkasan Konsistensi</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>λ_max (Lambda Max)</strong></td>
                                    <td class="text-right"><?= number_format($ahp_report['lambda_max'], 4) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>CI (Consistency Index)</strong></td>
                                    <td class="text-right"><?= number_format($ahp_report['ci'], 4) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>RI (Random Index)</strong></td>
                                    <td class="text-right"><?= number_format($ahp_report['ri'], 4) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>CR (Consistency Ratio)</strong></td>
                                    <td class="text-right">
                                        <span class="badge badge-<?= $ahp_report['cr'] <= 0.1 ? 'success' : 'danger' ?>">
                                            <?= number_format($ahp_report['cr'], 4) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status Konsistensi</strong></td>
                                    <td class="text-right">
                                        <?php if ($ahp_report['konsisten']): ?>
                                        <span class="badge badge-success">Konsisten (CR ≤ 0.1)</span>
                                        <?php else: ?>
                                        <span class="badge badge-danger">Tidak Konsisten (CR > 0.1)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Informasi</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Keterangan:</strong></p>
                            <ul class="mb-0">
                                <li><strong>λ_max:</strong> Nilai eigen maksimum</li>
                                <li><strong>CI:</strong> Consistency Index = (λ_max - n) / (n - 1)</li>
                                <li><strong>RI:</strong> Random Index (Saaty's values)</li>
                                <li><strong>CR:</strong> Consistency Ratio = CI / RI</li>
                                <li><strong>Konsisten jika:</strong> CR ≤ 0.1</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-warning">
        <i class="icon fas fa-exclamation-triangle"></i> 
        <strong>Matriks belum lengkap!</strong> 
        Lengkapi pairwise terlebih dahulu (<?= $matrix_data['filled_pairs'] ?>/<?= $matrix_data['total_pairs'] ?>).
        Tabel akan muncul otomatis setelah semua pairwise terisi.
    </div>
<?php endif; ?>