<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'periode';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/periode') ?>">Kelola Periode</a></li>
    <li class="breadcrumb-item active">Tambah Periode</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Periode Penilaian Baru</h3>
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
                            <i class="icon fas fa-ban"></i> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('tpp/periode/store') ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_periode">Nama Periode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_periode" name="nama_periode" 
                                           value="<?= old('nama_periode') ?>" required 
                                           placeholder="Contoh: Periode Penilaian Semester 1 2024">
                                    <small class="form-text text-muted">Nama periode untuk identifikasi</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tahun">Tahun <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="tahun" name="tahun" 
                                           value="<?= old('tahun', date('Y')) ?>" required min="2021" max="2030">
                                    <small class="form-text text-muted">Tahun periode penilaian</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="bulan">Bulan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="bulan" name="bulan" required>
                                        <option value="">Pilih Bulan</option>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?= $i ?>" <?= old('bulan') == $i ? 'selected' : '' ?>>
                                                <?= date('F', mktime(0, 0, 0, $i, 1)) ?> (<?= $i ?>)
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <small class="form-text text-muted">Bulan periode penilaian</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                                           value="<?= old('tanggal_mulai') ?>" required>
                                    <small class="form-text text-muted">Tanggal mulai periode penilaian</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tanggal_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" 
                                           value="<?= old('tanggal_selesai') ?>" required>
                                    <small class="form-text text-muted">Tanggal selesai periode penilaian</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="aktif" <?= old('status') == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="nonaktif" <?= old('status') == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                        <option value="selesai" <?= old('status') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <strong>Aktif:</strong> Periode sedang berjalan<br>
                                        <strong>Nonaktif:</strong> Periode belum/tidak aktif<br>
                                        <strong>Selesai:</strong> Periode sudah selesai
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                              placeholder="Keterangan tambahan tentang periode ini"><?= old('keterangan') ?></textarea>
                                    <small class="form-text text-muted">Opsional: Deskripsi atau catatan tentang periode</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Periode
                                    </button>
                                    <a href="<?= base_url('tpp/periode') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Set tanggal default
        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date().toISOString().split('T')[0];
            var nextMonth = new Date();
            nextMonth.setMonth(nextMonth.getMonth() + 1);
            var nextMonthStr = nextMonth.toISOString().split('T')[0];
            
            if (!document.getElementById('tanggal_mulai').value) {
                document.getElementById('tanggal_mulai').value = today;
            }
            if (!document.getElementById('tanggal_selesai').value) {
                document.getElementById('tanggal_selesai').value = nextMonthStr;
            }
            
            // Validasi tanggal selesai harus setelah tanggal mulai
            document.getElementById('tanggal_mulai').addEventListener('change', function() {
                var startDate = new Date(this.value);
                var endDateInput = document.getElementById('tanggal_selesai');
                var endDate = new Date(endDateInput.value);
                
                if (endDate < startDate) {
                    endDateInput.value = this.value;
                }
            });
        });
    </script>
<?= $this->endSection() ?>
