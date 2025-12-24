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
        <a href="<?= base_url('tpp/kriteria') ?>" class="nav-link">
            <i class="nav-icon fas fa-calculator"></i>
            <p>Input Bobot</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/kriteria') ?>" class="nav-link">
            <i class="nav-icon fas fa-check-circle"></i>
            <p>Validasi Konsistensi</p>
        </a>
    </li>
    <li class="nav-header">LAPORAN</li>
    <li class="nav-item">
        <a href="<?= base_url('tpp/kriteria') ?>" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Hasil ANP</p>
        </a>
    </li>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/kriteria') ?>">Kelola Kriteria</a></li>
    <li class="breadcrumb-item active">Edit Kriteria</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Kriteria</h3>
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
                    
                    <form action="<?= base_url('tpp/kriteria/update/' . $kriteria['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="kode">Kode Kriteria *</label>
                            <input type="text" class="form-control <?= session()->getFlashdata('errors.kode') ? 'is-invalid' : '' ?>" 
                                   id="kode" name="kode" value="<?= old('kode', $kriteria['kode']) ?>" 
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
                                   id="nama" name="nama" value="<?= old('nama', $kriteria['nama']) ?>" 
                                   placeholder="Contoh: Kedisiplinan, Kerja Sama, etc." required>
                            <?php if (session()->getFlashdata('errors.nama')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.nama') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Nama lengkap kriteria penilaian (3-100 karakter)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="bobot">Bobot Kriteria *</label>
                            <input type="number" step="0.001" min="0" max="1" 
                                   class="form-control <?= session()->getFlashdata('errors.bobot') ? 'is-invalid' : '' ?>" 
                                   id="bobot" name="bobot" value="<?= old('bobot', $kriteria['bobot']) ?>" 
                                   placeholder="Contoh: 0.15, 0.25, etc." required>
                            <?php if (session()->getFlashdata('errors.bobot')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.bobot') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Bobot kriteria antara 0 sampai 1 (contoh: 0.15 untuk 15%)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="jenis">Jenis Kriteria *</label>
                            <select class="form-control <?= session()->getFlashdata('errors.jenis') ? 'is-invalid' : '' ?>" 
                                    id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Benefit" <?= (old('jenis', $kriteria['jenis']) == 'Benefit') ? 'selected' : '' ?>>Benefit (Semakin tinggi semakin baik)</option>
                                <option value="Cost" <?= (old('jenis', $kriteria['jenis']) == 'Cost') ? 'selected' : '' ?>>Cost (Semakin rendah semakin baik)</option>
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
                                <i class="fas fa-save"></i> Update Kriteria
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
                    <h3 class="card-title">Informasi Kriteria</h3>
                </div>
                <div class="card-body">
                    <h5><i class="fas fa-info-circle text-info"></i> Detail Kriteria</h5>
                    <table class="table table-sm">
                        <tr>
                            <td>ID</td>
                            <td><strong><?= $kriteria['id'] ?></strong></td>
                        </tr>
                        <tr>
                            <td>Dibuat</td>
                            <td><?= date('d/m/Y H:i', strtotime($kriteria['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <td>Diupdate</td>
                            <td><?= date('d/m/Y H:i', strtotime($kriteria['updated_at'])) ?></td>
                        </tr>
                    </table>
                    
                    <h5><i class="fas fa-exclamation-triangle text-warning"></i> Perhatian</h5>
                    <div class="alert alert-warning">
                        <i class="icon fas fa-exclamation-triangle"></i> 
                        <strong>Perhatian:</strong> Mengubah bobot kriteria akan mempengaruhi hasil perhitungan ANP dan TOPSIS.
                    </div>
                    
                    <h5><i class="fas fa-lightbulb text-success"></i> Tips</h5>
                    <ul>
                        <li>Pastikan total bobot semua kriteria tetap = 1</li>
                        <li>Periksa konsistensi setelah mengubah bobot</li>
                        <li>Simpan perubahan secara berkala</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
