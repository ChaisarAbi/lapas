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
    <li class="nav-item">
        <a href="<?= base_url('tpp/anp/input-interdependensi') ?>" class="nav-link active">
            <i class="nav-icon fas fa-project-diagram"></i>
            <p>Input Interdependensi</p>
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
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/anp') ?>">Hasil ANP</a></li>
    <li class="breadcrumb-item active">Input Matriks Interdependensi ANP</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Input Matriks Interdependensi Analytic Network Process (ANP)</h3>
                    <div class="card-tools">
                        <?php if ($periode): ?>
                            <span class="badge badge-info">Periode: <?= $periode['nama_periode'] ?></span>
                        <?php endif; ?>
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
                        <h5><i class="icon fas fa-info-circle"></i> Petunjuk Input Matriks Interdependensi ANP</h5>
                        <p>ANP memperhitungkan interdependensi antar kriteria. Input nilai pengaruh kriteria terhadap kriteria lain menggunakan skala Saaty (1-9):</p>
                        <ul>
                            <li><strong>1</strong>: Sama pentingnya</li>
                            <li><strong>3</strong>: Sedikit lebih penting</li>
                            <li><strong>5</strong>: Lebih penting</li>
                            <li><strong>7</strong>: Sangat lebih penting</li>
                            <li><strong>9</strong>: Mutlak lebih penting</li>
                            <li><strong>2,4,6,8</strong>: Nilai antara</li>
                        </ul>
                        <p>Diagonal utama (kriteria terhadap diri sendiri) selalu 1.</p>
                    </div>
                    
                    <form action="<?= base_url('tpp/anp/simpan-interdependensi') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Kriteria</th>
                                        <?php foreach ($kriteria as $k): ?>
                                        <th width="10%" class="text-center">
                                            <span class="badge badge-info"><?= $k['kode'] ?></span><br>
                                            <small><?= substr($k['nama'], 0, 15) ?>...</small>
                                        </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Buat array untuk mapping nilai interdependensi
                                    $interdependensiMap = [];
                                    foreach ($interdependensi as $item) {
                                        $key = $item['kriteria_id_dari'] . '_' . $item['kriteria_id_ke'];
                                        $interdependensiMap[$key] = $item['nilai'];
                                    }
                                    ?>
                                    
                                    <?php foreach ($kriteria as $i => $kriteriaDari): ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td>
                                            <strong><?= $kriteriaDari['kode'] ?></strong><br>
                                            <small><?= $kriteriaDari['nama'] ?></small>
                                        </td>
                                        <?php foreach ($kriteria as $j => $kriteriaKe): ?>
                                        <td class="text-center">
                                            <?php 
                                            $key = $kriteriaDari['id'] . '_' . $kriteriaKe['id'];
                                            $nilai = isset($interdependensiMap[$key]) ? $interdependensiMap[$key] : ($i == $j ? 1 : 0);
                                            ?>
                                            <input type="number" 
                                                   name="interdependensi_<?= $i ?>_<?= $j ?>" 
                                                   value="<?= $nilai ?>"
                                                   min="0" 
                                                   max="9" 
                                                   step="0.1"
                                                   class="form-control form-control-sm text-center"
                                                   style="width: 80px; margin: 0 auto;"
                                                   <?= $i == $j ? 'readonly' : '' ?>>
                                            <?php if ($i == $j): ?>
                                                <small class="text-muted">(self)</small>
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
                                        <h3 class="card-title">Legenda Skala</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr><td>1</td><td>Sama pentingnya</td></tr>
                                            <tr><td>3</td><td>Sedikit lebih penting</td></tr>
                                            <tr><td>5</td><td>Lebih penting</td></tr>
                                            <tr><td>7</td><td>Sangat lebih penting</td></tr>
                                            <tr><td>9</td><td>Mutlak lebih penting</td></tr>
                                            <tr><td>2,4,6,8</td><td>Nilai antara</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Aksi</h3>
                                    </div>
                                    <div class="card-body">
                                        <button type="submit" class="btn btn-primary btn-block mb-2">
                                            <i class="fas fa-save"></i> Simpan Matriks Interdependensi
                                        </button>
                                        
                                        <a href="<?= base_url('tpp/anp') ?>" class="btn btn-info btn-block mb-2">
                                            <i class="fas fa-arrow-left"></i> Kembali ke Hasil ANP
                                        </a>
                                        
                                        <button type="button" class="btn btn-secondary btn-block" onclick="resetMatrix()">
                                            <i class="fas fa-redo"></i> Reset ke Nilai Default
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function resetMatrix() {
        if (confirm('Reset semua nilai ke default? Diagonal = 1, lainnya = 0')) {
            const inputs = document.querySelectorAll('input[type="number"]');
            inputs.forEach(input => {
                const name = input.name;
                const matches = name.match(/interdependensi_(\d+)_(\d+)/);
                if (matches) {
                    const i = parseInt(matches[1]);
                    const j = parseInt(matches[2]);
                    input.value = (i === j) ? 1 : 0;
                }
            });
        }
    }
    </script>
<?= $this->endSection() ?>
