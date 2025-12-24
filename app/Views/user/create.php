<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'users';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Manajemen User</a></li>
    <li class="breadcrumb-item active">Tambah User</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah User Baru</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('admin/users/store') ?>">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" 
                                   name="username" 
                                   id="username" 
                                   class="form-control" 
                                   value="<?= old('username') ?>"
                                   required>
                            <small class="text-muted">Username minimal 3 karakter</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control" 
                                   required>
                            <small class="text-muted">Password minimal 6 karakter</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   class="form-control" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" 
                                   name="nama_lengkap" 
                                   id="nama_lengkap" 
                                   class="form-control" 
                                   value="<?= old('nama_lengkap') ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">Pilih Role</option>
                                <option value="ADMIN" <?= old('role') == 'ADMIN' ? 'selected' : '' ?>>ADMIN</option>
                                <option value="TPP" <?= old('role') == 'TPP' ? 'selected' : '' ?>>TPP</option>
                                <option value="BIMKEMASWAT" <?= old('role') == 'BIMKEMASWAT' ? 'selected' : '' ?>>BIMKEMASWAT</option>
                                <option value="WALI_PEMASYARAKATAN" <?= old('role') == 'WALI_PEMASYARAKATAN' ? 'selected' : '' ?>>WALI_PEMASYARAKATAN</option>
                                <option value="KEPALA_LAPAS" <?= old('role') == 'KEPALA_LAPAS' ? 'selected' : '' ?>>KEPALA_LAPAS</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="<?= base_url('admin/users') ?>" class="btn btn-default">
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
                    <h3 class="card-title">Informasi Role</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <span class="badge badge-danger">ADMIN</span>
                            <span class="ml-2">Administrator sistem</span>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-info">TPP</span>
                            <span class="ml-2">Tim Pengamat Pemasyarakatan</span>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-success">BIMKEMASWAT</span>
                            <span class="ml-2">Petugas Bimbingan dan Perawatan</span>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-warning">WALI_PEMASYARAKATAN</span>
                            <span class="ml-2">Wali pembinaan narapidana</span>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-primary">KEPALA_LAPAS</span>
                            <span class="ml-2">Pengambil keputusan akhir</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
