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
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/subkriteria') ?>">Kelola Subkriteria</a></li>
    <li class="breadcrumb-item active">Subkriteria <?= $kriteria['nama'] ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Subkriteria untuk Kriteria: 
                        <span class="badge badge-info"><?= $kriteria['kode'] ?></span> 
                        <?= $kriteria['nama'] ?>
                        <span class="badge badge-<?= $kriteria['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                            <?= $kriteria['jenis'] ?>
                        </span>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('tpp/subkriteria/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Subkriteria
                        </a>
                        <a href="<?= base_url('tpp/subkriteria') ?>" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
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
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Bobot Subkriteria</span>
                                    <span class="info-box-number"><?= number_format($totalBobot, 3) ?></span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= min($totalBobot * 100, 100) ?>%"></div>
                                    </div>
                                    <span class="progress-description">
                                        <?php if ($totalBobot > 1): ?>
                                            <span class="text-danger">Total bobot melebihi 1!</span>
                                        <?php elseif ($totalBobot == 1): ?>
                                            <span class="text-success">Total bobot sempurna</span>
                                        <?php else: ?>
                                            <span class="text-warning">Masih bisa ditambah <?= number_format(1 - $totalBobot, 3) ?></span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Jumlah Subkriteria</span>
                                    <span class="info-box-number"><?= count($subkriteria) ?></span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= min(count($subkriteria) * 20, 100) ?>%"></div>
                                    </div>
                                    <span class="progress-description">
                                        <?php if (empty($subkriteria)): ?>
                                            <span class="text-danger">Belum ada subkriteria</span>
                                        <?php else: ?>
                                            <span class="text-success">Subkriteria tersedia</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (empty($subkriteria)): ?>
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Tidak ada subkriteria!</h5>
                            <p>Kriteria ini belum memiliki subkriteria. Silakan tambah subkriteria terlebih dahulu.</p>
                            <a href="<?= base_url('tpp/subkriteria/create') ?>" class="btn btn-warning">
                                <i class="fas fa-plus"></i> Tambah Subkriteria
                            </a>
                        </div>
                    <?php else: ?>
                        <form action="<?= base_url('tpp/subkriteria/update-bobot') ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="kriteria_id" value="<?= $kriteria['id'] ?>">
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama Subkriteria</th>
                                            <th>Jenis</th>
                                            <th>Bobot Saat Ini</th>
                                            <th>Bobot Baru</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($subkriteria as $item): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $item['kode'] ?></td>
                                                <td><?= $item['nama'] ?></td>
                                                <td>
                                                    <?php if ($item['jenis'] == 'Benefit'): ?>
                                                        <span class="badge badge-success">Benefit</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Cost</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info"><?= number_format($item['bobot'], 3) ?></span>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="subkriteria_id[]" value="<?= $item['id'] ?>">
                                                    <input type="number" step="0.001" min="0" max="1" 
                                                           class="form-control" name="bobot[]" 
                                                           value="<?= $item['bobot'] ?>" required>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('tpp/subkriteria/edit/' . $item['id']) ?>" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
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
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-right"><strong>Total Bobot:</strong></td>
                                            <td><span class="badge badge-info"><?= number_format($totalBobot, 3) ?></span></td>
                                            <td>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fas fa-save"></i> Simpan Perubahan Bobot
                                                </button>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </form>
                        
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info-circle"></i> Petunjuk Pengaturan Bobot</h5>
                            <ul>
                                <li>Total bobot semua subkriteria untuk kriteria ini harus ≤ 1</li>
                                <li>Bobot menentukan seberapa penting subkriteria dalam penilaian</li>
                                <li>Pastikan total bobot tidak melebihi 1 sebelum menyimpan</li>
                                <li>Untuk menghapus subkriteria, klik tombol <i class="fas fa-trash text-danger"></i></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
