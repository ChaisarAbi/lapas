<?= $this->extend('layouts/dashboard_template') ?>

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
        <a href="<?= base_url('tpp/anp/pairwise-comparison') ?>" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>Pairwise Comparison</p>
        </a>
    </li>
    <li class="nav-header">LAPORAN</li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/anp') ?>" class="nav-link active">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Hasil ANP</p>
        </a>
    </li>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Hasil Analytic Network Process (ANP)</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hasil Analytic Network Process (ANP)</h3>
                   
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
                        <h5><i class="icon fas fa-info-circle"></i> Tentang Analytic Network Process (ANP)</h5>
                        <p>ANP adalah metode pengambilan keputusan yang memperhitungkan interdependensi antar kriteria. Hasil ANP berupa bobot akhir yang digunakan dalam perhitungan TOPSIS.</p>
                    </div>
                    
                    <?php if ($periode): ?>
                        <div class="alert alert-info">
                            <i class="icon fas fa-calendar-alt"></i> 
                            Periode Aktif: <strong><?= $periode['nama_periode'] ?></strong> | 
                            <?= date('d F Y', strtotime($periode['tanggal_mulai'])) ?> - <?= date('d F Y', strtotime($periode['tanggal_selesai'])) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($hasilAnp)): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Ringkasan Hasil ANP</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="60%">Jumlah Kriteria</td>
                                            <td class="text-right"><?= $hasilAnp['n'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>λ Maksimum (λmax)</td>
                                            <td class="text-right"><?= number_format($hasilAnp['lambda_max'], 4) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Consistency Index (CI)</td>
                                            <td class="text-right"><?= number_format($hasilAnp['ci'], 4) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Random Index (RI)</td>
                                            <td class="text-right"><?= number_format($hasilAnp['ri'], 4) ?></td>
                                        </tr>
                                        <tr class="<?= $hasilAnp['konsisten'] ? 'table-success' : 'table-danger' ?>">
                                            <td><strong>Consistency Ratio (CR)</strong></td>
                                            <td class="text-right"><strong><?= number_format($hasilAnp['cr'], 4) ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status Konsistensi</strong></td>
                                            <td class="text-right">
                                                <span class="badge badge-<?= $hasilAnp['konsisten'] ? 'success' : 'danger' ?>">
                                                    <?= $hasilAnp['konsisten'] ? 'KONSISTEN' : 'TIDAK KONSISTEN' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Aksi</h3>
                                </div>
                                <div class="card-body">
                                    <form action="<?= base_url('tpp/anp/simpan-bobot-akhir') ?>" method="post">
                                        <?= csrf_field() ?>
                                        
                                        <?php foreach ($subkriteria as $index => $sk): ?>
                                            <input type="hidden" name="subkriteria_id[]" value="<?= $sk['id'] ?>">
                                            <input type="hidden" name="bobot_akhir[]" value="<?= $hasilAnp['bobot_akhir'][$index] ?>">
                                        <?php endforeach; ?>
                                        
                                            <button type="submit" class="btn btn-primary btn-block mb-2" 
                                                    <?= !$hasilAnp['konsisten'] ? 'disabled' : '' ?>>
                                                <i class="fas fa-save"></i> Simpan Bobot Global ANP ke Database
                                            </button>
                                        
                                        <?php if (!$hasilAnp['konsisten']): ?>
                                            <div class="alert alert-warning">
                                                <i class="icon fas fa-exclamation-triangle"></i> 
                                                Tidak dapat menyimpan karena matriks tidak konsisten (CR > 0.1)
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                    
                                    <a href="<?= base_url('tpp/anp/pairwise-comparison') ?>" class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-edit"></i> Edit Pairwise Comparison
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Navigation -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header p-0">
                                    <ul class="nav nav-tabs" id="anp-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="bobot-tab" data-toggle="tab" href="#bobot" role="tab" aria-controls="bobot" aria-selected="true">
                                                <i class="fas fa-weight"></i> Bobot Akhir
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="false">
                                                <i class="fas fa-calculator"></i> Detail Perhitungan
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="anp-tabs-content">
                                        <!-- Tab 1: Bobot Akhir -->
                                        <div class="tab-pane fade show active" id="bobot" role="tabpanel" aria-labelledby="bobot-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">Rank</th>
                                                            <th width="10%">Kode</th>
                                                            <th width="35%">Nama Kriteria</th>
                                                            <th width="15%">Jenis</th>
                                                            <th width="15%">Bobot Prioritas</th>
                                                        <th width="15%">Bobot Global ANP</th>
                                                            <th width="5%">Persentase</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        // Gabungkan data subkriteria dengan bobot akhir untuk sorting
                                                        $subkriteriaBobot = [];
                                                        foreach ($subkriteria as $index => $sk) {
                                                            $subkriteriaBobot[] = [
                                                                'subkriteria' => $sk,
                                                                'bobot_akhir' => $hasilAnp['bobot_akhir'][$index],
                                                                'bobot' => $hasilAnp['bobot'][$index]['weight']
                                                            ];
                                                        }
                                                        
                                                        // Urutkan berdasarkan bobot akhir (descending)
                                                        usort($subkriteriaBobot, function($a, $b) {
                                                            return $b['bobot_akhir'] <=> $a['bobot_akhir'];
                                                        });
                                                        ?>
                                                        
                                                        <?php foreach ($subkriteriaBobot as $rank => $item): ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <span class="badge badge-<?= 
                                                                    $rank == 0 ? 'success' : 
                                                                    ($rank == 1 ? 'info' : 
                                                                    ($rank == 2 ? 'warning' : 'secondary'))
                                                                ?>">
                                                                    <?= $rank + 1 ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-info"><?= $item['subkriteria']['kode'] ?></span>
                                                            </td>
                                                            <td>
                                                                <?= $item['subkriteria']['nama'] ?><br>
                                                                <small class="text-muted">(<?= $item['subkriteria']['kriteria_nama'] ?>)</small>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-<?= $item['subkriteria']['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                                                                    <?= $item['subkriteria']['jenis'] ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-right"><?= number_format($item['bobot'], 4) ?></td>
                                                            <td class="text-right">
                                                                <span class="badge badge-<?= 
                                                                    $item['bobot_akhir'] >= 0.2 ? 'primary' : 
                                                                    ($item['bobot_akhir'] >= 0.1 ? 'info' : 'secondary')
                                                                ?>">
                                                                    <?= number_format($item['bobot_akhir'], 4) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="progress progress-xs">
                                                                    <div class="progress-bar bg-primary" 
                                                                         style="width: <?= $item['bobot_akhir'] * 100 ?>%"></div>
                                                                </div>
                                                                <small><?= number_format($item['bobot_akhir'] * 100, 1) ?>%</small>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="bg-light">
                                                            <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                                            <td class="text-right">
                                                                <strong><?= number_format(array_sum(array_column($subkriteriaBobot, 'bobot')), 4) ?></strong>
                                                            </td>
                                                            <td class="text-right">
                                                                <strong><?= number_format(array_sum($hasilAnp['bobot_akhir']), 4) ?></strong>
                                                            </td>
                                                            <td class="text-right">
                                                                <strong>100%</strong>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <!-- Tab 2: Detail Perhitungan -->
                                        <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                                            <div class="alert alert-info">
                                                <h5><i class="fas fa-info-circle"></i> Detail Perhitungan ANP</h5>
                                                <p>Berikut adalah langkah-langkah perhitungan Analytic Network Process (ANP):</p>
                                            </div>
                                            
                                            <!-- Matriks Interdependensi -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">1. Matriks Interdependensi</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <p>Matriks interdependensi berukuran <?= $hasilAnp['n'] ?> × <?= $hasilAnp['n'] ?> yang merepresentasikan pengaruh antar subkriteria (node).</p>
                                                            <p><strong>Skala Saaty (1-9):</strong></p>
                                                            <ul>
                                                                <li>1: Sama pentingnya</li>
                                                                <li>3: Sedikit lebih penting</li>
                                                                <li>5: Lebih penting</li>
                                                                <li>7: Sangat lebih penting</li>
                                                                <li>9: Mutlak lebih penting</li>
                                                            </ul>
                                                            <p>Diagonal utama = 1 (self-comparison)</p>
                                                            
                                                            <div class="table-responsive mt-3">
                                                                <table class="table table-bordered table-sm">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th class="text-center">Node</th>
                                                                            <?php foreach ($subkriteria as $sk): ?>
                                                                                <th class="text-center"><?= $sk['kode'] ?><br><small><?= $sk['nama'] ?></small></th>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($subkriteria as $i => $sk_i): ?>
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                <strong><?= $sk_i['kode'] ?></strong><br>
                                                                                <small><?= $sk_i['nama'] ?></small>
                                                                            </td>
                                                                            <?php foreach ($subkriteria as $j => $sk_j): ?>
                                                                                <td class="text-center">
                                                                                    <?= number_format($hasilAnp['supermatrix'][$i][$j], 4) ?>
                                                                                </td>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Unweighted Supermatrix -->
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">2. Unweighted Supermatrix (Normalisasi Kolom)</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <p>Unweighted supermatrix adalah matriks interdependensi yang telah dinormalisasi sehingga jumlah setiap kolom = 1.</p>
                                                            
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th class="text-center">Node</th>
                                                                            <?php foreach ($subkriteria as $sk): ?>
                                                                                <th class="text-center"><?= $sk['kode'] ?><br><small><?= $sk['nama'] ?></small></th>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($subkriteria as $i => $sk_i): ?>
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                <strong><?= $sk_i['kode'] ?></strong><br>
                                                                                <small><?= $sk_i['nama'] ?></small>
                                                                            </td>
                                                                            <?php foreach ($subkriteria as $j => $sk_j): ?>
                                                                                <td class="text-center">
                                                                                    <?= number_format($hasilAnp['unweighted_supermatrix'][$i][$j], 4) ?>
                                                                                </td>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Weighted Supermatrix -->
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">3. Weighted Supermatrix (Dengan Bobot Cluster)</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <p>Weighted supermatrix adalah unweighted supermatrix yang telah dikalikan dengan bobot cluster.</p>
                                                            
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th class="text-center">Node</th>
                                                                            <?php foreach ($subkriteria as $sk): ?>
                                                                                <th class="text-center"><?= $sk['kode'] ?><br><small><?= $sk['nama'] ?></small></th>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($subkriteria as $i => $sk_i): ?>
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                <strong><?= $sk_i['kode'] ?></strong><br>
                                                                                <small><?= $sk_i['nama'] ?></small>
                                                                            </td>
                                                                            <?php foreach ($subkriteria as $j => $sk_j): ?>
                                                                                <td class="text-center">
                                                                                    <?= number_format($hasilAnp['weighted_supermatrix'][$i][$j], 4) ?>
                                                                                </td>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Limit Supermatrix -->
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">4. Limit Supermatrix (Hasil Konvergensi)</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <p>Limit supermatrix diperoleh dengan mengangkat weighted supermatrix ke pangkat yang cukup besar hingga konvergen.</p>
                                                            <p><strong>Rumus:</strong> W<sup>∞</sup> = lim<sub>k→∞</sub> W<sup>k</sup></p>
                                                            
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th class="text-center">Node</th>
                                                                            <?php foreach ($subkriteria as $sk): ?>
                                                                                <th class="text-center"><?= $sk['kode'] ?><br><small><?= $sk['nama'] ?></small></th>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($subkriteria as $i => $sk_i): ?>
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                <strong><?= $sk_i['kode'] ?></strong><br>
                                                                                <small><?= $sk_i['nama'] ?></small>
                                                                            </td>
                                                                            <?php foreach ($subkriteria as $j => $sk_j): ?>
                                                                                <td class="text-center">
                                                                                    <?= number_format($hasilAnp['limit_supermatrix'][$i][$j], 4) ?>
                                                                                </td>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Bobot Eigen dan Konsistensi -->
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">5. Bobot Eigen (Eigenvector)</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <p>Bobot eigen dihitung dari matriks interdependensi menggunakan metode eigenvalue.</p>
                                                            
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th class="text-center">No</th>
                                                                            <th class="text-center">Kode</th>
                                                                            <th class="text-center">Nama Kriteria</th>
                                                                            <th class="text-center">Bobot Eigen</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($hasilAnp['bobot'] as $index => $bobot): ?>
                                                                        <tr>
                                                                            <td class="text-center"><?= $index + 1 ?></td>
                                                                            <td class="text-center"><?= $subkriteria[$index]['kode'] ?></td>
                                                                            <td><?= $subkriteria[$index]['nama'] ?></td>
                                                                            <td class="text-right"><?= number_format($bobot['weight'], 4) ?></td>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr class="bg-light">
                                                                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                                            <td class="text-right"><strong><?= number_format(array_sum(array_column($hasilAnp['bobot'], 'weight')), 4) ?></strong></td>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">6. Analisis Konsistensi</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-sm">
                                                                <tr>
                                                                    <td>λ Maksimum (λmax)</td>
                                                                    <td class="text-right"><?= number_format($hasilAnp['lambda_max'], 4) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Consistency Index (CI)</td>
                                                                    <td class="text-right"><?= number_format($hasilAnp['ci'], 4) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Random Index (RI)</td>
                                                                    <td class="text-right"><?= number_format($hasilAnp['ri'], 4) ?></td>
                                                                </tr>
                                                                <tr class="<?= $hasilAnp['konsisten'] ? 'table-success' : 'table-danger' ?>">
                                                                    <td><strong>Consistency Ratio (CR)</strong></td>
                                                                    <td class="text-right"><strong><?= number_format($hasilAnp['cr'], 4) ?></strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Status</strong></td>
                                                                    <td class="text-right">
                                                                        <span class="badge badge-<?= $hasilAnp['konsisten'] ? 'success' : 'danger' ?>">
                                                                            <?= $hasilAnp['konsisten'] ? 'KONSISTEN (CR ≤ 0.1)' : 'TIDAK KONSISTEN (CR > 0.1)' ?>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <p class="text-muted"><small>CR = CI / RI. Jika CR ≤ 0.1, matriks konsisten.</small></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Bobot Global ANP -->
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">7. Bobot Global ANP (Hasil Akhir)</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <p>Bobot global ANP diambil dari kolom pertama limit supermatrix yang telah dinormalisasi.</p>
                                                            
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th class="text-center">Rank</th>
                                                                            <th class="text-center">Kode</th>
                                                                            <th class="text-center">Nama Kriteria</th>
                                                                            <th class="text-center">Bobot Eigen</th>
                                                                            <th class="text-center">Bobot Global ANP</th>
                                                                            <th class="text-center">Persentase</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php 
                                                                        // Gabungkan data subkriteria dengan bobot akhir untuk sorting
                                                                        $subkriteriaBobot = [];
                                                                        foreach ($subkriteria as $index => $sk) {
                                                                            $subkriteriaBobot[] = [
                                                                                'subkriteria' => $sk,
                                                                                'bobot_akhir' => $hasilAnp['bobot_akhir'][$index],
                                                                                'bobot' => $hasilAnp['bobot'][$index]['weight']
                                                                            ];
                                                                        }
                                                                        
                                                                        // Urutkan berdasarkan bobot akhir (descending)
                                                                        usort($subkriteriaBobot, function($a, $b) {
                                                                            return $b['bobot_akhir'] <=> $a['bobot_akhir'];
                                                                        });
                                                                        ?>
                                                                        
                                                                        <?php foreach ($subkriteriaBobot as $rank => $item): ?>
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-<?= 
                                                                                    $rank == 0 ? 'success' : 
                                                                                    ($rank == 1 ? 'info' : 
                                                                                    ($rank == 2 ? 'warning' : 'secondary'))
                                                                                ?>">
                                                                                    <?= $rank + 1 ?>
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-center"><?= $item['subkriteria']['kode'] ?></td>
                                                                            <td><?= $item['subkriteria']['nama'] ?></td>
                                                                            <td class="text-right"><?= number_format($item['bobot'], 4) ?></td>
                                                                            <td class="text-right">
                                                                                <span class="badge badge-<?= 
                                                                                    $item['bobot_akhir'] >= 0.2 ? 'primary' : 
                                                                                    ($item['bobot_akhir'] >= 0.1 ? 'info' : 'secondary')
                                                                                ?>">
                                                                                    <?= number_format($item['bobot_akhir'], 4) ?>
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-center"><?= number_format($item['bobot_akhir'] * 100, 1) ?>%</td>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr class="bg-light">
                                                                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                                            <td class="text-right"><strong><?= number_format(array_sum(array_column($subkriteriaBobot, 'bobot')), 4) ?></strong></td>
                                                                            <td class="text-right"><strong><?= number_format(array_sum($hasilAnp['bobot_akhir']), 4) ?></strong></td>
                                                                            <td class="text-right"><strong>100%</strong></td>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
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
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Distribusi Bobot Berdasarkan Jenis</h3>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $totalBenefit = 0;
                                    $totalCost = 0;
                                    
                                    // Hitung ulang total benefit dan cost
                                    foreach ($subkriteria as $index => $sk) {
                                        if ($sk['jenis'] == 'Benefit') {
                                            $totalBenefit += $hasilAnp['bobot_akhir'][$index];
                                        } else {
                                            $totalCost += $hasilAnp['bobot_akhir'][$index];
                                        }
                                    }
                                    ?>
                                    
                                    <div class="progress-group">
                                        <span class="progress-text">Kriteria Benefit</span>
                                        <span class="float-right">
                                            <b><?= number_format($totalBenefit, 4) ?></b> / <?= number_format($totalBenefit * 100, 1) ?>%
                                        </span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-success" style="width: <?= $totalBenefit * 100 ?>%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="progress-group">
                                        <span class="progress-text">Kriteria Cost</span>
                                        <span class="float-right">
                                            <b><?= number_format($totalCost, 4) ?></b> / <?= number_format($totalCost * 100, 1) ?>%
                                        </span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-danger" style="width: <?= $totalCost * 100 ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Interpretasi Hasil</h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Kriteria dengan bobot tertinggi</strong> adalah yang paling berpengaruh dalam evaluasi narapidana.</p>
                                    <p><strong>Kriteria benefit</strong> (warna hijau) semakin tinggi nilai semakin baik.</p>
                                    <p><strong>Kriteria cost</strong> (warna merah) semakin rendah nilai semakin baik.</p>
                                    <p class="text-muted"><small>Bobot global ANP ini akan digunakan dalam perhitungan TOPSIS untuk ranking narapidana.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Perhitungan ANP Belum Dilakukan</h5>
                        <p>Untuk menghitung hasil ANP, Anda perlu:</p>
                        <ol>
                            <li><strong>Gunakan Pairwise Comparison:</strong> <a href="<?= base_url('tpp/anp/pairwise-comparison') ?>" class="btn btn-primary btn-sm">Pairwise Comparison</a></li>
                        </ol>
                        <p class="text-muted"><small>Setelah pairwise comparison diisi, sistem akan secara otomatis menghitung hasil ANP saat Anda membuka halaman ini.</small></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
