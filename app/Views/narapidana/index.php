<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'narapidana';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Narapidana</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Narapidana</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/narapidana/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus mr-1"></i> Tambah Narapidana
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Sukses!</h5>
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
                    
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No. Registrasi</th>
                                <th>Nama Lengkap</th>
                                <th>Jenis Kelamin</th>
                                <th>Kasus</th>
                                <th>Masa Tahanan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($narapidana as $napi): ?>
                            <tr>
                                <td><?= $napi['nomor_registrasi'] ?></td>
                                <td><?= $napi['nama_lengkap'] ?></td>
                                <td><?= $napi['jenis_kelamin'] ?></td>
                                <td><?= $napi['kasus'] ?></td>
                                <td><?= $napi['masa_tahanan'] ?> tahun</td>
                                <td>
                                    <span class="badge badge-<?= 
                                        $napi['status'] == 'Aktif' ? 'danger' :
                                        ($napi['status'] == 'Bebas' ? 'success' : 'warning')
                                    ?>">
                                        <?= $napi['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/narapidana/edit/' . $napi['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= base_url('admin/narapidana/delete/' . $napi['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data narapidana ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
