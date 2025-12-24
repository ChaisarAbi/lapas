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
        <a href="<?= base_url('tpp/subkriteria') ?>" class="nav-link active">
            <i class="nav-icon fas fa-list-alt"></i>
            <p>Kelola Subkriteria</p>
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
    <li class="breadcrumb-item active">Kelola Subkriteria</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Subkriteria</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('tpp/subkriteria/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Subkriteria
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Subkriteria</th>
                                    <th>Kriteria</th>
                                    <th>Bobot</th>
                                    <th>Jenis</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($subkriteria)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada data subkriteria.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($subkriteria as $item): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $item['kode'] ?></td>
                                            <td><?= $item['nama'] ?></td>
                                            <td>
                                                <span class="badge badge-info"><?= $item['kriteria_kode'] ?></span>
                                                <?= $item['kriteria_nama'] ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-success"><?= number_format($item['bobot'], 3) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($item['jenis'] == 'Benefit'): ?>
                                                    <span class="badge badge-success">Benefit</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Cost</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('tpp/subkriteria/edit/' . $item['id']) ?>" 
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('tpp/subkriteria/by/' . $item['kriteria_id']) ?>" 
                                                   class="btn btn-info btn-sm" title="Lihat per Kriteria">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('tpp/subkriteria/delete/' . $item['id']) ?>" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Yakin ingin menghapus subkriteria ini?')" 
                                                   title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informasi:</h5>
                            <ul>
                                <li>Subkriteria adalah detail dari kriteria penilaian</li>
                                <li>Total bobot subkriteria per kriteria harus ≤ 1</li>
                                <li>Klik <strong>Lihat per Kriteria</strong> untuk mengatur bobot</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Statistik:</h5>
                            <ul>
                                <li>Total Subkriteria: <strong><?= count($subkriteria) ?></strong></li>
                                <li>Total Kriteria: <strong><?= count($kriteria) ?></strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
