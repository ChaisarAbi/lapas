<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'users';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Manajemen User</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar User</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus mr-1"></i> Tambah User
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
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['nama_lengkap'] ?></td>
                                <td>
                                    <span class="badge badge-<?= 
                                        $user['role'] == 'ADMIN' ? 'danger' :
                                        ($user['role'] == 'TPP' ? 'info' :
                                        ($user['role'] == 'BIMKEMASWAT' ? 'success' :
                                        ($user['role'] == 'WALI_PEMASYARAKATAN' ? 'warning' : 'primary')))
                                    ?>">
                                        <?= $user['role'] ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= base_url('admin/users/delete/' . $user['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
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
