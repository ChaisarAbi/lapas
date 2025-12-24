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
        <a href="<?= base_url('tpp/subkriteria') ?>" class="nav-link active">
            <i class="nav-icon fas fa-list-alt"></i>
            <p>Kelola Subkriteria</p>
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
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/subkriteria') ?>">Kelola Subkriteria</a></li>
    <li class="breadcrumb-item active">Edit Subkriteria</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Subkriteria</h3>
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
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('tpp/subkriteria/update/' . $subkriteria['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="kriteria_id">Kriteria Induk *</label>
                            <select class="form-control <?= session()->getFlashdata('errors.kriteria_id') ? 'is-invalid' : '' ?>" 
                                    id="kriteria_id" name="kriteria_id" required>
                                <option value="">-- Pilih Kriteria --</option>
                                <?php foreach ($kriteria as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= (old('kriteria_id') ?? $subkriteria['kriteria_id']) == $k['id'] ? 'selected' : '' ?>>
                                        <?= $k['kode'] ?> - <?= $k['nama'] ?> (<?= $k['jenis'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session()->getFlashdata('errors.kriteria_id')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.kriteria_id') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Pilih kriteria yang akan menjadi induk subkriteria ini</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="kode">Kode Subkriteria *</label>
                            <input type="text" class="form-control <?= session()->getFlashdata('errors.kode') ? 'is-invalid' : '' ?>" 
                                   id="kode" name="kode" value="<?= old('kode') ?? $subkriteria['kode'] ?>" 
                                   placeholder="Contoh: C1.1, K2.1, etc." required>
                            <?php if (session()->getFlashdata('errors.kode')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.kode') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Kode unik untuk identifikasi subkriteria (maksimal 20 karakter)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama">Nama Subkriteria *</label>
                            <input type="text" class="form-control <?= session()->getFlashdata('errors.nama') ? 'is-invalid' : '' ?>" 
                                   id="nama" name="nama" value="<?= old('nama') ?? $subkriteria['nama'] ?>" 
                                   placeholder="Contoh: Kehadiran, Kerja Tim, etc." required>
                            <?php if (session()->getFlashdata('errors.nama')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.nama') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Nama lengkap subkriteria penilaian (maksimal 255 karakter)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="bobot">Bobot Subkriteria *</label>
                            <input type="number" step="0.001" min="0" max="1" 
                                   class="form-control <?= session()->getFlashdata('errors.bobot') ? 'is-invalid' : '' ?>" 
                                   id="bobot" name="bobot" value="<?= old('bobot') ?? $subkriteria['bobot'] ?>" 
                                   placeholder="Contoh: 0.15, 0.25, etc." required>
                            <?php if (session()->getFlashdata('errors.bobot')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.bobot') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Bobot subkriteria antara 0 sampai 1 (contoh: 0.15 untuk 15%)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="jenis">Jenis Subkriteria *</label>
                            <select class="form-control <?= session()->getFlashdata('errors.jenis') ? 'is-invalid' : '' ?>" 
                                    id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Benefit" <?= (old('jenis') ?? $subkriteria['jenis']) == 'Benefit' ? 'selected' : '' ?>>Benefit (Semakin tinggi semakin baik)</option>
                                <option value="Cost" <?= (old('jenis') ?? $subkriteria['jenis']) == 'Cost' ? 'selected' : '' ?>>Cost (Semakin rendah semakin baik)</option>
                            </select>
                            <?php if (session()->getFlashdata('errors.jenis')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.jenis') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">
                                Benefit: Nilai tinggi = baik (contoh: kehadiran)<br>
                                Cost: Nilai rendah = baik (contoh: pelanggaran)
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Subkriteria
                            </button>
                            <a href="<?= base_url('tpp/subkriteria') ?>" class="btn btn-default">
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
                    <h3 class="card-title">Informasi Subkriteria</h3>
                </div>
                <div class="card-body">
                    <h5><i class="fas fa-info-circle text-info"></i> Detail Subkriteria</h5>
                    <ul>
                        <li><strong>Kriteria Induk:</strong> <?= $subkriteria['kriteria_kode'] ?> - <?= $subkriteria['kriteria_nama'] ?></li>
                        <li><strong>Kode:</strong> <?= $subkriteria['kode'] ?></li>
                        <li><strong>Nama:</strong> <?= $subkriteria['nama'] ?></li>
                        <li><strong>Bobot:</strong> <?= number_format($subkriteria['bobot'], 3) ?></li>
                        <li><strong>Jenis:</strong> 
                            <?php if ($subkriteria['jenis'] == 'Benefit'): ?>
                                <span class="badge badge-success">Benefit</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Cost</span>
                            <?php endif; ?>
                        </li>
                        <li><strong>Dibuat:</strong> <?= date('d/m/Y H:i', strtotime($subkriteria['created_at'])) ?></li>
                        <li><strong>Diupdate:</strong> <?= date('d/m/Y H:i', strtotime($subkriteria['updated_at'])) ?></li>
                    </ul>
                    
                    <h5><i class="fas fa-lightbulb text-warning"></i> Catatan Penting</h5>
                    <ul>
                        <li>Pastikan kode subkriteria unik untuk kriteria yang sama</li>
                        <li>Total bobot subkriteria per kriteria harus ≤ 1</li>
                        <li>Perubahan bobot akan mempengaruhi perhitungan ANP</li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <i class="icon fas fa-lightbulb"></i> <strong>Tip:</strong> Pastikan data subkriteria akurat untuk hasil penilaian yang optimal.
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
