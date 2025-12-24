<?= $this->extend('layouts/dashboard_template') ?>

<?= $this->section('sidebar_menu') ?>
    <li class="nav-item">
        <a href="<?= base_url('bimkesmaswat/dashboard') ?>" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-header">PENILAIAN</li>
    <li class="nav-item">
        <a href="<?= base_url('bimkesmaswat/penilaian') ?>" class="nav-link">
            <i class="nav-icon fas fa-clipboard-list"></i>
            <p>Input Nilai</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('bimkesmaswat/penilaian') ?>" class="nav-link">
            <i class="nav-icon fas fa-user-injured"></i>
            <p>Data Narapidana</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('bimkesmaswat/penilaian/riwayat') ?>" class="nav-link">
            <i class="nav-icon fas fa-history"></i>
            <p>Riwayat Penilaian</p>
        </a>
    </li>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item active">Dashboard</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bimbingan dan Perawatan (BIMKEMASWAT)</h3>
                </div>
                <div class="card-body">
                    <p>Selamat datang di dashboard Petugas Bimbingan dan Perawatan. Anda bertanggung jawab untuk:</p>
                    <ul>
                        <li>Menginput nilai penilaian setiap narapidana</li>
                        <li>Menyimpan nilai per kriteria yang telah ditentukan</li>
                        <li>Memantau perkembangan narapidana</li>
                    </ul>
                    
                    <div class="alert alert-success">
                        <h5><i class="icon fas fa-check-circle"></i> Petunjuk Penilaian</h5>
                        <p>Input nilai berdasarkan observasi langsung terhadap narapidana. Nilai yang diinput akan digunakan untuk perhitungan TOPSIS.</p>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-user-injured"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Narapidana Aktif</span>
                                    <span class="info-box-number"><?= $totalNarapidanaAktif ?? 0 ?></span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Periode <?= $periodeAktif ?? date('Y-m') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-clipboard-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sudah Dinilai</span>
                                    <span class="info-box-number"><?= $totalSudahDinilai ?? 0 ?></span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= $persentaseSelesai ?? 0 ?>%"></div>
                                    </div>
                                    <span class="progress-description">
                                        <?= $persentaseSelesai ?? 0 ?>% selesai
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Belum Dinilai</span>
                                    <span class="info-box-number"><?= $totalBelumDinilai ?? 0 ?></span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= $totalNarapidanaAktif > 0 ? (($totalBelumDinilai ?? 0) / $totalNarapidanaAktif) * 100 : 0 ?>%"></div>
                                    </div>
                                    <span class="progress-description">
                                        <?= $totalNarapidanaAktif > 0 ? round((($totalBelumDinilai ?? 0) / $totalNarapidanaAktif) * 100, 1) : 0 ?>% dari total
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Periode Aktif</span>
                                    <span class="info-box-number"><?= $periodeAktif ?? date('Y-m') ?></span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Penilaian bulan ini
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabel Narapidana Belum Dinilai -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Narapidana Belum Dinilai</h3>
                                    <div class="card-tools">
                                        <span class="badge badge-warning"><?= $totalBelumDinilai ?? 0 ?> orang</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($narapidanaBelumDinilai)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>No. Registrasi</th>
                                                        <th>Nama</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($narapidanaBelumDinilai as $index => $napi): ?>
                                                        <?php if ($index < 5): ?>
                                                        <tr>
                                                            <td><?= $napi['nomor_registrasi'] ?></td>
                                                            <td><?= $napi['nama_lengkap'] ?></td>
                                                            <td>
                                                                <a href="<?= base_url('bimkesmaswat/penilaian') ?>" class="btn btn-xs btn-primary">
                                                                    <i class="fas fa-edit"></i> Input Nilai
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php if (count($narapidanaBelumDinilai) > 5): ?>
                                            <div class="text-center mt-2">
                                                <a href="<?= base_url('bimkesmaswat/penilaian') ?>" class="btn btn-sm btn-default">
                                                    Lihat semua (<?= count($narapidanaBelumDinilai) ?>)
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-check-circle fa-3x text-success"></i>
                                            <p class="mt-2">Semua narapidana sudah dinilai</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Penilaian Terbaru</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($penilaianTerbaru)): ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($penilaianTerbaru as $penilaian): ?>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?= $penilaian['nama_lengkap'] ?></strong><br>
                                                        <small class="text-muted"><?= $penilaian['nomor_registrasi'] ?></small>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="badge badge-info"><?= date('d/m', strtotime($penilaian['created_at'])) ?></span><br>
                                                        <small class="text-muted"><?= date('H:i', strtotime($penilaian['created_at'])) ?></small>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-history fa-3x text-muted"></i>
                                            <p class="mt-2">Belum ada penilaian</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Call to Action -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="callout callout-info">
                                <h5><i class="fas fa-bullhorn"></i> Aksi Cepat</h5>
                                <p>Gunakan tombol di bawah untuk langsung menginput nilai:</p>
                                <div class="row">
                                    <div class="col-md-4">
                                        <a href="<?= base_url('bimkesmaswat/penilaian') ?>" class="btn btn-primary btn-block">
                                            <i class="fas fa-clipboard-list"></i> Input Nilai
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="<?= base_url('bimkesmaswat/penilaian/riwayat') ?>" class="btn btn-success btn-block">
                                            <i class="fas fa-history"></i> Lihat Riwayat
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="<?= base_url('bimkesmaswat/penilaian') ?>" class="btn btn-warning btn-block">
                                            <i class="fas fa-user-injured"></i> Data Narapidana
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
