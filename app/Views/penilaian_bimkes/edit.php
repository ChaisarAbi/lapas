<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'penilaian';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/penilaian/riwayat') ?>">Riwayat Penilaian</a></li>
    <li class="breadcrumb-item active">Edit Nilai Penilaian</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Nilai Penilaian</h3>
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
                    
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('bimkesmaswat/penilaian/update/' . $penilaian['id']) ?>">
                        <div class="form-group">
                            <label>Narapidana</label>
                            <input type="text" class="form-control" value="<?= $narapidana['nama_lengkap'] ?> (<?= $narapidana['nomor_registrasi'] ?>)" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>Kriteria</label>
                            <input type="text" class="form-control" value="<?= $kriteria['kode'] ?> - <?= $kriteria['nama'] ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="nilai">Nilai (0-100)</label>
                            <input type="number" 
                                   name="nilai" 
                                   id="nilai" 
                                   class="form-control" 
                                   min="0" 
                                   max="100" 
                                   step="0.01"
                                   value="<?= old('nilai', $penilaian['nilai']) ?>"
                                   required>
                            <small class="text-muted">Masukkan nilai antara 0 sampai 100</small>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="<?= base_url('bimkesmaswat/penilaian/riwayat') ?>" class="btn btn-default">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Penilaian</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Periode</th>
                            <td><?= $penilaian['periode'] ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Input</th>
                            <td><?= date('d/m/Y H:i', strtotime($penilaian['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <th>Jenis Kriteria</th>
                            <td>
                                <span class="badge badge-<?= $kriteria['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                                    <?= $kriteria['jenis'] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Bobot Kriteria</th>
                            <td><?= number_format($kriteria['bobot'], 3) ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if ($penilaian['nilai'] >= 70): ?>
                                    <span class="badge badge-success">Baik</span>
                                <?php elseif ($penilaian['nilai'] >= 50): ?>
                                    <span class="badge badge-warning">Cukup</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Perlu Perhatian</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
