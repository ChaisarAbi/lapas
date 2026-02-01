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
                                <h4>Nilai Subkriteria</h4>
                                <p class="text-muted">Masukkan nilai untuk setiap subkriteria (skala 0-100)</p>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    Sistem sekarang menggunakan subkriteria untuk perhitungan yang lebih akurat.
                                    Setiap kriteria memiliki beberapa subkriteria dengan bobot berbeda.
                                </div>
                            </div>
                        </div>
                        
                        <?php 
                        // Group subkriteria by kriteria untuk tampilan yang lebih terorganisir
                        $subkriteria_by_kriteria = [];
                        foreach ($subkriteria as $sub) {
                            $kriteria_id = $sub['kriteria_id'];
                            if (!isset($subkriteria_by_kriteria[$kriteria_id])) {
                                $subkriteria_by_kriteria[$kriteria_id] = [];
                            }
                            $subkriteria_by_kriteria[$kriteria_id][] = $sub;
                        }
                        ?>
                        
                        <?php foreach ($subkriteria_by_kriteria as $kriteria_id => $subs): 
                            $kriteria_info = null;
                            foreach ($kriteria as $k) {
                                if ($k['id'] == $kriteria_id) {
                                    $kriteria_info = $k;
                                    break;
                                }
                            }
                        ?>
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <?= $kriteria_info ? $kriteria_info['kode'] . ' - ' . $kriteria_info['nama'] : 'Kriteria ' . $kriteria_id ?>
                                    <span class="badge badge-<?= $kriteria_info && $kriteria_info['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                                        <?= $kriteria_info ? $kriteria_info['jenis'] : 'Benefit' ?>
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($subs as $sub): 
                                        $skala_info = '';
                                        if ($sub['jenis'] == 'Benefit') {
                                            $skala_info = 'Skala: 0-20=1, 21-40=2, 41-60=3, 61-80=4, 81-100=5';
                                        } elseif ($sub['jenis'] == 'Cost') {
                                            $skala_info = 'Skala: 0-33=1, 34-66=2, 67-100=3';
                                        }
                                    ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nilai_<?= $sub['id'] ?>">
                                                <?= $sub['kode'] ?> - <?= $sub['nama'] ?>
                                                <span class="badge badge-<?= $sub['jenis'] == 'Benefit' ? 'success' : 'danger' ?>">
                                                    <?= $sub['jenis'] ?>
                                                </span>
                                            </label>
                                            <input type="number" 
                                                   name="nilai_<?= $sub['id'] ?>" 
                                                   id="nilai_<?= $sub['id'] ?>" 
                                                   class="form-control nilai-input" 
                                                   data-jenis="<?= $sub['jenis'] ?>"
                                                   min="0" 
                                                   max="100" 
                                                   step="0.01"
                                                   placeholder="Masukkan nilai 0-100"
                                                   oninput="updateSkalaInfo(this)">
                                            <div class="skala-info" id="skala_info_<?= $sub['id'] ?>" style="font-size: 0.85rem; margin-top: 5px;">
                                                <small class="text-muted"><?= $skala_info ?></small><br>
                                                <small class="text-info">Nilai konversi: <span id="konversi_<?= $sub['id'] ?>">-</span></small>
                                            </div>
                                            <small class="text-muted">Bobot: <?= number_format($sub['bobot'], 3) ?></small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <script>
                        function updateSkalaInfo(input) {
                            const nilai = parseFloat(input.value) || 0;
                            const jenis = input.getAttribute('data-jenis');
                            const id = input.id.replace('nilai_', '');
                            const konversiSpan = document.getElementById('konversi_' + id);
                            
                            let konversi = 0;
                            if (jenis === 'Benefit') {
                                if (nilai >= 0 && nilai <= 20) konversi = 1;
                                else if (nilai >= 21 && nilai <= 40) konversi = 2;
                                else if (nilai >= 41 && nilai <= 60) konversi = 3;
                                else if (nilai >= 61 && nilai <= 80) konversi = 4;
                                else if (nilai >= 81 && nilai <= 100) konversi = 5;
                            } else if (jenis === 'Cost') {
                                if (nilai >= 0 && nilai <= 33) konversi = 1;
                                else if (nilai >= 34 && nilai <= 66) konversi = 2;
                                else if (nilai >= 67 && nilai <= 100) konversi = 3;
                            }
                            
                            konversiSpan.textContent = konversi > 0 ? konversi : '-';
                        }
                        
                        // Initialize skala info for all inputs
                        document.addEventListener('DOMContentLoaded', function() {
                            const inputs = document.querySelectorAll('.nilai-input');
                            inputs.forEach(input => {
                                updateSkalaInfo(input);
                            });
                        });
                        </script>
                        
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
