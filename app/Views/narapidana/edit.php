<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'narapidana';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/narapidana') ?>">Data Narapidana</a></li>
    <li class="breadcrumb-item active">Edit Narapidana</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Data Narapidana</h3>
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
                    
                    <form method="post" action="<?= base_url('admin/narapidana/update/' . $narapidana['id']) ?>">
                        <input type="hidden" name="id" value="<?= $narapidana['id'] ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor_registrasi">Nomor Registrasi</label>
                                    <input type="text" 
                                           name="nomor_registrasi" 
                                           id="nomor_registrasi" 
                                           class="form-control" 
                                           value="<?= old('nomor_registrasi', $narapidana['nomor_registrasi']) ?>"
                                           required>
                                    <small class="text-muted">Nomor registrasi unik untuk narapidana</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nama_lengkap">Nama Lengkap</label>
                                    <input type="text" 
                                           name="nama_lengkap" 
                                           id="nama_lengkap" 
                                           class="form-control" 
                                           value="<?= old('nama_lengkap', $narapidana['nama_lengkap']) ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="jenis_kelamin">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" <?= old('jenis_kelamin', $narapidana['jenis_kelamin']) == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="Perempuan" <?= old('jenis_kelamin', $narapidana['jenis_kelamin']) == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tempat_lahir">Tempat Lahir</label>
                                    <input type="text" 
                                           name="tempat_lahir" 
                                           id="tempat_lahir" 
                                           class="form-control" 
                                           value="<?= old('tempat_lahir', $narapidana['tempat_lahir']) ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir</label>
                                    <input type="date" 
                                           name="tanggal_lahir" 
                                           id="tanggal_lahir" 
                                           class="form-control" 
                                           value="<?= old('tanggal_lahir', $narapidana['tanggal_lahir']) ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea name="alamat" 
                                              id="alamat" 
                                              class="form-control" 
                                              rows="3" 
                                              required><?= old('alamat', $narapidana['alamat']) ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="kasus">Kasus</label>
                                    <textarea name="kasus" 
                                              id="kasus" 
                                              class="form-control" 
                                              rows="3" 
                                              required><?= old('kasus', $narapidana['kasus']) ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="masa_tahanan">Masa Tahanan (tahun)</label>
                                    <input type="number" 
                                           name="masa_tahanan" 
                                           id="masa_tahanan" 
                                           class="form-control" 
                                           min="1" 
                                           max="100"
                                           value="<?= old('masa_tahanan', $narapidana['masa_tahanan']) ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tanggal_masuk">Tanggal Masuk</label>
                                    <input type="date" 
                                           name="tanggal_masuk" 
                                           id="tanggal_masuk" 
                                           class="form-control" 
                                           value="<?= old('tanggal_masuk', $narapidana['tanggal_masuk']) ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Aktif" <?= old('status', $narapidana['status']) == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="Bebas" <?= old('status', $narapidana['status']) == 'Bebas' ? 'selected' : '' ?>>Bebas</option>
                                        <option value="Pindah" <?= old('status', $narapidana['status']) == 'Pindah' ? 'selected' : '' ?>>Pindah</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="<?= base_url('admin/narapidana') ?>" class="btn btn-default">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
