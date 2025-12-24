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
        <a href="<?= base_url('tpp/bobot/matriks') ?>" class="nav-link">
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
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/kriteria') ?>">Kelola Kriteria</a></li>
    <li class="breadcrumb-item active">Tambah Kriteria</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Kriteria Baru</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> Validasi Gagal!</h5>
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('tpp/kriteria/store') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="kode">Kode Kriteria *</label>
                            <input type="text" class="form-control <?= session()->getFlashdata('errors.kode') ? 'is-invalid' : '' ?>" 
                                   id="kode" name="kode" value="<?= old('kode') ?>" 
                                   placeholder="Contoh: C1, K2, etc." required>
                            <?php if (session()->getFlashdata('errors.kode')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.kode') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Kode unik untuk identifikasi kriteria (2-10 karakter)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama">Nama Kriteria *</label>
                            <input type="text" class="form-control <?= session()->getFlashdata('errors.nama') ? 'is-invalid' : '' ?>" 
                                   id="nama" name="nama" value="<?= old('nama') ?>" 
                                   placeholder="Contoh: Kedisiplinan, Kerja Sama, etc." required>
                            <?php if (session()->getFlashdata('errors.nama')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.nama') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Nama lengkap kriteria penilaian (3-100 karakter)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="jenis">Jenis Kriteria *</label>
                            <select class="form-control <?= session()->getFlashdata('errors.jenis') ? 'is-invalid' : '' ?>" 
                                    id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Benefit" <?= old('jenis') == 'Benefit' ? 'selected' : '' ?>>Benefit (Semakin tinggi semakin baik)</option>
                                <option value="Cost" <?= old('jenis') == 'Cost' ? 'selected' : '' ?>>Cost (Semakin rendah semakin baik)</option>
                            </select>
                            <?php if (session()->getFlashdata('errors.jenis')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.jenis') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">
                                Benefit: Nilai tinggi = baik (contoh: kedisiplinan)<br>
                                Cost: Nilai rendah = baik (contoh: pelanggaran)
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Kriteria
                            </button>
                            <a href="<?= base_url('tpp/kriteria') ?>" class="btn btn-default">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Petunjuk Pengisian</h3>
                </div>
                <div class="card-body">
                    <h5><i class="fas fa-info-circle text-info"></i> Tentang Kriteria</h5>
                    <p>Kriteria adalah faktor-faktor yang digunakan untuk menilai narapidana dalam program pembinaan.</p>
                    
                    <h5><i class="fas fa-balance-scale text-success"></i> Jenis Kriteria</h5>
                    <ul>
                        <li><strong>Benefit</strong>: Semakin tinggi nilai semakin baik</li>
                        <li><strong>Cost</strong>: Semakin rendah nilai semakin baik</li>
                    </ul>
                    
                    <h5><i class="fas fa-lightbulb text-warning"></i> Catatan Penting</h5>
                    <ul>
                        <li>Bobot kriteria diatur di menu <strong>Input Bobot</strong></li>
                        <li>Subkriteria dapat ditambahkan setelah kriteria dibuat</li>
                        <li>Pastikan kode kriteria unik dan tidak duplikat</li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <i class="icon fas fa-lightbulb"></i> <strong>Tip:</strong> Buat kriteria yang relevan dengan program pembinaan narapidana.
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
