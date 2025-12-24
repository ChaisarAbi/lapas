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
        <a href="<?= base_url('tpp/bobot') ?>" class="nav-link">
            <i class="nav-icon fas fa-calculator"></i>
            <p>Input Bobot</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/bobot/matriks') ?>" class="nav-link active">
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
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/bobot') ?>">Input Bobot</a></li>
    <li class="breadcrumb-item active">Matriks Perbandingan Berpasangan</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Matriks Perbandingan Berpasangan</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('tpp/bobot/konsistensi') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-check-circle"></i> Validasi Konsistensi
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
                    
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info-circle"></i> Petunjuk Pengisian Matriks</h5>
                        <p>Matriks perbandingan berpasangan digunakan untuk menentukan tingkat kepentingan relatif antar kriteria.</p>
                        <p><strong>Skala Perbandingan (Saaty Scale):</strong></p>
                        <ul>
                            <li><strong>1</strong> = Kedua kriteria sama penting</li>
                            <li><strong>3</strong> = Kriteria i sedikit lebih penting dari j</li>
                            <li><strong>5</strong> = Kriteria i lebih penting dari j</li>
                            <li><strong>7</strong> = Kriteria i sangat lebih penting dari j</li>
                            <li><strong>9</strong> = Kriteria i mutlak lebih penting dari j</li>
                            <li><strong>2,4,6,8</strong> = Nilai antara</li>
                            <li><strong>1/3, 1/5, 1/7, 1/9</strong> = Kebalikan (jika j lebih penting dari i)</li>
                        </ul>
                    </div>
                    
                    <form action="<?= base_url('tpp/bobot/simpan-matriks') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th width="15%">Kriteria</th>
                                        <?php foreach ($kriteria as $k): ?>
                                        <th width="<?= 85 / count($kriteria) ?>%" class="text-center">
                                            <span class="badge badge-info"><?= $k['kode'] ?></span><br>
                                            <small><?= substr($k['nama'], 0, 15) ?>...</small>
                                        </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kriteria as $i => $kriteriaI): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $kriteriaI['kode'] ?></strong><br>
                                            <small><?= $kriteriaI['nama'] ?></small>
                                        </td>
                                        <?php foreach ($kriteria as $j => $kriteriaJ): ?>
                                        <td class="text-center">
                                            <?php if ($i == $j): ?>
                                                <input type="number" class="form-control form-control-sm text-center" 
                                                       value="1" readonly style="background-color: #f8f9fa;">
                                                <input type="hidden" name="matriks[<?= $i ?>][<?= $j ?>]" value="1">
                                            <?php elseif ($i < $j): ?>
                                                <select class="form-control form-control-sm" name="matriks[<?= $i ?>][<?= $j ?>]" required>
                                                    <option value="">-- Pilih --</option>
                                                    <option value="9">9 (Mutlak lebih penting)</option>
                                                    <option value="8">8</option>
                                                    <option value="7">7 (Sangat lebih penting)</option>
                                                    <option value="6">6</option>
                                                    <option value="5">5 (Lebih penting)</option>
                                                    <option value="4">4</option>
                                                    <option value="3">3 (Sedikit lebih penting)</option>
                                                    <option value="2">2</option>
                                                    <option value="1" selected>1 (Sama penting)</option>
                                                    <option value="1/2">1/2</option>
                                                    <option value="1/3">1/3 (Sedikit kurang penting)</option>
                                                    <option value="1/4">1/4</option>
                                                    <option value="1/5">1/5 (Kurang penting)</option>
                                                    <option value="1/6">1/6</option>
                                                    <option value="1/7">1/7 (Sangat kurang penting)</option>
                                                    <option value="1/8">1/8</option>
                                                    <option value="1/9">1/9 (Mutlak kurang penting)</option>
                                                </select>
                                            <?php else: ?>
                                                <input type="text" class="form-control form-control-sm text-center" 
                                                       value="Auto" readonly style="background-color: #f8f9fa;">
                                                <small class="text-muted">Otomatis</small>
                                            <?php endif; ?>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Keterangan Matriks</h3>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Diagonal utama:</strong> Selalu 1 (kriteria dibandingkan dengan diri sendiri)</p>
                                        <p><strong>Segitiga atas:</strong> Isi dengan nilai perbandingan</p>
                                        <p><strong>Segitiga bawah:</strong> Otomatis terisi kebalikan dari segitiga atas</p>
                                        <p class="text-muted"><small>Contoh: Jika A vs B = 3, maka B vs A = 1/3</small></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Aksi</h3>
                                    </div>
                                    <div class="card-body">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-save"></i> Simpan Matriks
                                        </button>
                                        <a href="<?= base_url('tpp/bobot') ?>" class="btn btn-default btn-block">
                                            <i class="fas fa-arrow-left"></i> Kembali ke Input Bobot
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
