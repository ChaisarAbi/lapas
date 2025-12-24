<?= $this->extend('layouts/dashboard_template') ?>

<?= $this->section('sidebar_menu') ?>
    <li class="nav-item">
        <a href="<?= base_url('admin/dashboard') ?>" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-header">MANAJEMEN</li>
    <li class="nav-item">
        <a href="<?= base_url('admin/users') ?>" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>Manajemen User</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('admin/narapidana') ?>" class="nav-link">
            <i class="nav-icon fas fa-user-injured"></i>
            <p>Data Narapidana</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('admin/perhitungan/topsis') ?>" class="nav-link active">
            <i class="nav-icon fas fa-clipboard-list"></i>
            <p>Perhitungan TOPSIS</p>
        </a>
    </li>
    <li class="nav-header">LAPORAN</li>
    <li class="nav-item">
        <a href="<?= base_url('admin/perhitungan/topsis') ?>" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Statistik Ranking</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('admin/perhitungan/cetak') ?>" class="nav-link">
            <i class="nav-icon fas fa-file-pdf"></i>
            <p>Laporan PDF</p>
        </a>
    </li>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Perhitungan TOPSIS</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Perhitungan Ranking dengan Metode TOPSIS</h3>
                    <div class="card-tools">
                        <form method="get" class="form-inline">
                            <div class="input-group input-group-sm">
                                <select name="periode" class="form-control" onchange="this.form.submit()">
                                    <option value="">Pilih Periode</option>
                                    <?php if (isset($periode_list) && !empty($periode_list)): ?>
                                        <?php foreach ($periode_list as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= ($periode ?? '') == $key ? 'selected' : '' ?>><?= $value ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="<?= date('Y-m') ?>" selected><?= date('F Y') ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="icon fas fa-ban"></i> <?= $error ?>
                        </div>
                    <?php else: ?>
                        
                        <?php if (!empty($ranking)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>No. Registrasi</th>
                                        <th>Nama Narapidana</th>
                                        <th>Kasus</th>
                                        <th>Jarak Positif (D+)</th>
                                        <th>Jarak Negatif (D-)</th>
                                        <th>Nilai Preferensi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ranking as $index => $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $index == 0 ? 'success' : 
                                                ($index == 1 ? 'info' : 
                                                ($index == 2 ? 'warning' : 'secondary'))
                                            ?>">
                                                <?= $index + 1 ?>
                                            </span>
                                        </td>
                                        <td><?= $item['narapidana']['nomor_registrasi'] ?></td>
                                        <td><?= $item['narapidana']['nama_lengkap'] ?></td>
                                        <td><?= $item['narapidana']['kasus'] ?></td>
                                        <td><?= number_format($item['d_positif'], 4) ?></td>
                                        <td><?= number_format($item['d_negatif'], 4) ?></td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $item['preferensi'] >= 0.8 ? 'success' : 
                                                ($item['preferensi'] >= 0.6 ? 'info' : 
                                                ($item['preferensi'] >= 0.4 ? 'warning' : 'danger'))
                                            ?>">
                                                <?= number_format($item['preferensi'], 4) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $item['narapidana']['status'] == 'Aktif' ? 'danger' :
                                                ($item['narapidana']['status'] == 'Bebas' ? 'success' : 'warning')
                                            ?>">
                                                <?= $item['narapidana']['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Interpretasi Hasil</h3>
                                    </div>
                                    <div class="card-body">
                                        <p>Metode TOPSIS (Technique for Order Preference by Similarity to Ideal Solution) menghasilkan ranking berdasarkan nilai preferensi (Ci) yang dihitung dari:</p>
                                        <p><strong>Ci = D- / (D+ + D-)</strong></p>
                                        <p>Dimana:</p>
                                        <ul>
                                            <li><strong>D+</strong>: Jarak ke solusi ideal positif</li>
                                            <li><strong>D-</strong>: Jarak ke solusi ideal negatif</li>
                                        </ul>
                                        <p>Semakin tinggi nilai Ci, semakin baik alternatif tersebut.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Kriteria yang Digunakan</h3>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($kriteria as $k): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= $k['kode'] ?>: <?= $k['nama'] ?>
                                                <span class="badge badge-<?= $k['jenis'] == 'Benefit' ? 'success' : 'danger' ?> badge-pill">
                                                    Bobot: <?= number_format($k['bobot'], 3) ?>
                                                </span>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="icon fas fa-exclamation-triangle"></i> Tidak ada data ranking untuk ditampilkan.
                        </div>
                        <?php endif; ?>
                        
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('admin/perhitungan/cetak?periode=' . ($periode ?? date('Y-m'))) ?>" class="btn btn-primary" target="_blank">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </a>
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
