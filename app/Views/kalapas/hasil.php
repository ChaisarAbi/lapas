<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'hasil';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('kalapas/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Hasil Penilaian</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hasil Penilaian Narapidana</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <form method="get" action="<?= base_url('kalapas/hasil') ?>" class="form-inline">
                                <select name="periode" class="form-control" onchange="this.form.submit()">
                                    <option value="">Pilih Periode</option>
                                    <?php foreach ($periode_list as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= $periode == $key ? 'selected' : '' ?>><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($penilaian)): ?>
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Data Tidak Tersedia</h5>
                            <p>Tidak ada data penilaian untuk periode <strong><?= $periode ?></strong>. Pastikan:</p>
                            <ol>
                                <li>Sudah ada input penilaian dari petugas BIMKEMASWAT</li>
                                <li>Periode yang dipilih sesuai dengan periode penilaian</li>
                            </ol>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i> Menampilkan data untuk periode <strong><?= $periode ?></strong>
                        </div>
                        
                        <?php
                        // Kelompokkan penilaian berdasarkan narapidana
                        $groupedPenilaian = [];
                        foreach ($penilaian as $item) {
                            $narapidanaId = $item['narapidana_id'];
                            if (!isset($groupedPenilaian[$narapidanaId])) {
                                $groupedPenilaian[$narapidanaId] = [
                                    'nama_lengkap' => $item['nama_lengkap'],
                                    'nomor_registrasi' => $item['nomor_registrasi'],
                                    'periode' => $item['periode'],
                                    'items' => [],
                                    'total_nilai' => 0,
                                    'count' => 0,
                                ];
                            }
                            $groupedPenilaian[$narapidanaId]['items'][] = $item;
                            $groupedPenilaian[$narapidanaId]['total_nilai'] += $item['nilai'];
                            $groupedPenilaian[$narapidanaId]['count']++;
                        }
                        
                        // Hitung rata-rata per narapidana
                        foreach ($groupedPenilaian as &$group) {
                            $group['rata_rata'] = $group['count'] > 0 ? $group['total_nilai'] / $group['count'] : 0;
                        }
                        ?>
                        
                        <div class="row">
                            <?php foreach ($groupedPenilaian as $narapidanaId => $group): ?>
                            <div class="col-md-6">
                                <div class="card card-primary card-outline collapsed-card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-user mr-2"></i>
                                            <?= $group['nama_lengkap'] ?>
                                            <small class="text-muted">(<?= $group['nomor_registrasi'] ?>)</small>
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <span class="badge badge-light" style="font-size: 1.2em; padding: 5px 10px;">
                                                <?= number_format($group['rata_rata'], 1) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="text-muted">
                                                    Periode: <span class="badge badge-info"><?= $group['periode'] ?></span>
                                                    | Total Kriteria: <?= $group['count'] ?>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr class="bg-light">
                                                        <th width="5%">No</th>
                                                        <th width="30%">Kriteria</th>
                                                        <th width="15%">Nilai</th>
                                                        <th width="20%">Status</th>
                                                        <th width="30%">Penilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($group['items'] as $index => $item): ?>
                                                    <tr>
                                                        <td><?= $index + 1 ?></td>
                                                        <td><?= $item['kode'] ?> - <?= $item['kriteria_nama'] ?></td>
                                                        <td><?= number_format($item['nilai'], 2) ?></td>
                                                        <td>
                                                            <?php if ($item['nilai'] >= 70): ?>
                                                                <span class="badge badge-success">Baik</span>
                                                            <?php elseif ($item['nilai'] >= 50): ?>
                                                                <span class="badge badge-warning">Cukup</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger">Perlu Perhatian</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= $item['penilai_nama'] ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="callout callout-info">
                                                    <h5><i class="fas fa-info-circle"></i> Ringkasan</h5>
                                                    <p>
                                                        Rata-rata nilai: <strong><?= number_format($group['rata_rata'], 2) ?></strong> |
                                                        Status keseluruhan: 
                                                        <?php if ($group['rata_rata'] >= 70): ?>
                                                            <span class="badge badge-success">Baik</span>
                                                        <?php elseif ($group['rata_rata'] >= 50): ?>
                                                            <span class="badge badge-warning">Cukup</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">Perlu Perhatian</span>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Statistik Hasil Penilaian (Periode <?= $periode ?>)</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-success">
                                                    <span class="info-box-icon"><i class="fas fa-thumbs-up"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Narapidana</span>
                                                        <span class="info-box-number"><?= $totalNarapidana ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: 100%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            Narapidana aktif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-clipboard-check"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Penilaian</span>
                                                        <span class="info-box-number"><?= $totalPenilaian ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: 100%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            Data penilaian
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-warning">
                                                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Rata-rata Nilai</span>
                                                        <?php 
                                                        $totalNilai = 0;
                                                        foreach ($penilaian as $item) {
                                                            $totalNilai += $item['nilai'];
                                                        }
                                                        $rataNilai = $totalPenilaian > 0 ? $totalNilai / $totalPenilaian : 0;
                                                        ?>
                                                        <span class="info-box-number"><?= number_format($rataNilai, 2) ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: <?= $rataNilai ?>%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            Skala 0-100
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-primary">
                                                    <span class="info-box-icon"><i class="fas fa-file-pdf"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Laporan</span>
                                                        <span class="info-box-number">PDF</span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: 100%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            <a href="<?= base_url('kalapas/ranking/cetak?periode=' . $periode) ?>" target="_blank" class="text-white">Cetak Laporan</a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i> Data ini digunakan sebagai dasar pengambilan keputusan.
                            </p>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="<?= base_url('kalapas/ranking?periode=' . $periode) ?>" class="btn btn-primary">
                                <i class="fas fa-chart-bar"></i> Lihat Ranking
                            </a>
                            <a href="<?= base_url('kalapas/validasi?periode=' . $periode) ?>" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Validasi Hasil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
