<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'users';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Manajemen User</a></li>
    <li class="breadcrumb-item active">Edit User</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit User</h3>
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
                    
                    <form method="post" action="<?= base_url('admin/users/update/' . $user['id']) ?>">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" 
                                   name="username" 
                                   id="username" 
                                   class="form-control" 
                                   value="<?= old('username', $user['username']) ?>"
                                   required>
                            <small class="text-muted">Username minimal 3 karakter</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control">
                            <small class="text-muted">Password minimal 6 karakter</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" 
                                   name="nama_lengkap" 
                                   id="nama_lengkap" 
                                   class="form-control" 
                                   value="<?= old('nama_lengkap', $user['nama_lengkap']) ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">Pilih Role</option>
                                <option value="ADMIN" <?= old('role', $user['role']) == 'ADMIN' ? 'selected' : '' ?>>ADMIN</option>
                                <option value="TPP" <?= old('role', $user['role']) == 'TPP' ? 'selected' : '' ?>>TPP</option>
                                <option value="BIMKEMASWAT" <?= old('role', $user['role']) == 'BIMKEMASWAT' ? 'selected' : '' ?>>BIMKEMASWAT</option>
                                <option value="WALI_PEMASYARAKATAN" <?= old('role', $user['role']) == 'WALI_PEMASYARAKATAN' ? 'selected' : '' ?>>WALI_PEMASYARAKATAN</option>
                                <option value="KEPALA_LAPAS" <?= old('role', $user['role']) == 'KEPALA_LAPAS' ? 'selected' : '' ?>>KEPALA_LAPAS</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
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
                    <h3 class="card-title">Informasi User</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID User</th>
                            <td><?= $user['id'] ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td><?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if ($user['deleted_at']): ?>
                                    <span class="badge badge-danger">Terhapus</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="alert alert-info mt-3">
                        <h5><i class="icon fas fa-info-circle"></i> Catatan</h5>
                        <p>Password hanya perlu diisi jika ingin mengubah password user. Jika tidak ingin mengubah password, biarkan kolom password kosong.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
