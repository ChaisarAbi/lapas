<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'penilaian';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Input Nilai Penilaian</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Input Nilai Penilaian Narapidana</h3>
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
                    
                    <form method="post" action="<?= base_url('bimkesmaswat/penilaian/save') ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="periode">Periode Penilaian</label>
                                    <select name="periode" id="periode" class="form-control" required>
                                        <option value="">Pilih Periode</option>
                                        <?php foreach ($periode_list as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= $periode == $key ? 'selected' : '' ?>><?= $value ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($periode_aktif): ?>
                                        <small class="text-success">
                                            <i class="fas fa-info-circle"></i> Periode aktif saat ini: 
                                            <?= $periode_aktif['nama_periode'] ?> (<?= $periode_aktif['tahun'] ?>-<?= str_pad($periode_aktif['bulan'], 2, '0', STR_PAD_LEFT) ?>)
                                        </small>
                                    <?php else: ?>
                                        <small class="text-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Tidak ada periode aktif. Silakan hubungi TPP.
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="narapidana_id">Nama Narapidana</label>
                                    <select name="narapidana_id" id="narapidana_id" class="form-control" required>
                                        <option value="">Pilih Narapidana</option>
                                        <?php foreach ($narapidana as $n): ?>
                                            <option value="<?= $n['id'] ?>"><?= $n['nama_lengkap'] ?> (<?= $n['nomor_registrasi'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Nilai Kriteria</h4>
                                <p class="text-muted">Masukkan nilai untuk setiap kriteria (skala 0-100)</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <?php foreach ($kriteria as $index => $k): ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nilai_<?= $k['id'] ?>">
                                        <?= $k['kode'] ?> - <?= $k['nama'] ?>
                                        <span class="badge badge-<?= $k['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                                            <?= $k['jenis'] ?>
                                        </span>
                                    </label>
                                    <input type="number" 
                                           name="nilai_<?= $k['id'] ?>" 
                                           id="nilai_<?= $k['id'] ?>" 
                                           class="form-control" 
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           placeholder="Masukkan nilai 0-100">
                                    <small class="text-muted">Bobot: <?= number_format($k['bobot'], 3) ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Nilai
                                </button>
                                <a href="<?= base_url('bimkesmaswat/penilaian/riwayat') ?>" class="btn btn-info">
                                    <i class="fas fa-history"></i> Lihat Riwayat
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
