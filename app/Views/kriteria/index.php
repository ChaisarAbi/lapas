<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'kriteria';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Kelola Kriteria</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Kriteria Penilaian</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('tpp/kriteria/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Kriteria
                        </a>
                    </div>
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
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Kode</th>
                                    <th width="30%">Nama Kriteria</th>
                                    <th width="10%">Bobot</th>
                                    <th width="15%">Jenis</th>
                                    <th width="20%">Dibuat</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($kriteria)): ?>
                                    <?php foreach ($kriteria as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><span class="badge badge-info"><?= $item['kode'] ?></span></td>
                                        <td><?= $item['nama'] ?></td>
                                        <td><?= number_format($item['bobot'], 3) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $item['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                                                <?= $item['jenis'] ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= base_url('tpp/kriteria/edit/' . $item['id']) ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('tpp/kriteria/delete/' . $item['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin menghapus kriteria ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data kriteria</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Total Bobot</h3>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $totalBobot = 0;
                                    if (!empty($kriteria)) {
                                        foreach ($kriteria as $item) {
                                            $totalBobot += $item['bobot'];
                                        }
                                    }
                                    ?>
                                    <h2 class="text-center <?= $totalBobot == 1 ? 'text-success' : ($totalBobot > 1 ? 'text-danger' : 'text-warning') ?>">
                                        <?= number_format($totalBobot, 3) ?>
                                    </h2>
                                    <p class="text-center">
                                        <?php if ($totalBobot == 1): ?>
                                            <span class="badge badge-success">Bobot sudah normal (total = 1)</span>
                                        <?php elseif ($totalBobot > 1): ?>
                                            <span class="badge badge-danger">Bobot melebihi 1</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Bobot kurang dari 1</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Distribusi Kriteria</h3>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $benefitCount = 0;
                                    $costCount = 0;
                                    if (!empty($kriteria)) {
                                        foreach ($kriteria as $item) {
                                            if ($item['jenis'] == 'Benefit') {
                                                $benefitCount++;
                                            } else {
                                                $costCount++;
                                            }
                                        }
                                    }
                                    ?>
                                    <p>Benefit: <span class="badge badge-success"><?= $benefitCount ?> kriteria</span></p>
                                    <p>Cost: <span class="badge badge-danger"><?= $costCount ?> kriteria</span></p>
                                    <p>Total: <span class="badge badge-info"><?= count($kriteria) ?> kriteria</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> Total bobot semua kriteria harus sama dengan 1 untuk perhitungan ANP yang valid.
                    </p>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
