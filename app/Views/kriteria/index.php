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
                                        <td colspan="5" class="text-center">Tidak ada data kriteria</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Kriteria</h3>
                                </div>
                                <div class="card-body">
                                    <p>Total Kriteria: <span class="badge badge-info"><?= count($kriteria) ?> kriteria</span></p>
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle"></i> Kriteria berfungsi sebagai cluster untuk pengelompokan subkriteria (node).
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Catatan Penting</h3>
                                </div>
                                <div class="card-body">
                                    <ul>
                                        <li>Kriteria hanya untuk pengelompokan subkriteria (node)</li>
                                        <li>Semua kriteria memiliki nilai setara</li>
                                        <li>Bobot untuk TOPSIS diambil dari bobot global subkriteria hasil ANP</li>
                                        <li>Subkriteria dapat ditambahkan setelah kriteria dibuat</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> Gunakan fitur ini untuk mengelola kriteria penilaian dalam sistem ANP.
                    </p>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>