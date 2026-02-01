<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'periode';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Kelola Periode</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Periode Penilaian</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/periode/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Periode
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
                    
                    <?php if ($active_periode): ?>
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info-circle"></i> Periode Aktif Saat Ini</h5>
                            <p><strong><?= $active_periode['nama_periode'] ?></strong> (<?= $active_periode['tahun'] ?>-<?= str_pad($active_periode['bulan'], 2, '0', STR_PAD_LEFT) ?>)</p>
                            <p>Tanggal: <?= date('d/m/Y', strtotime($active_periode['tanggal_mulai'])) ?> - <?= date('d/m/Y', strtotime($active_periode['tanggal_selesai'])) ?></p>
                            <p>Status: <span class="badge badge-success">Aktif</span></p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="icon fas fa-exclamation-triangle"></i> Tidak ada periode aktif. Silakan tambah periode baru dan atur status menjadi aktif.
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama Periode</th>
                                    <th width="15%">Tahun-Bulan</th>
                                    <th width="20%">Tanggal</th>
                                    <th width="10%">Status</th>
                                    <th width="20%">Keterangan</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($periodes as $index => $periode): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $periode['nama_periode'] ?></td>
                                    <td><?= $periode['tahun'] ?>-<?= str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT) ?></td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($periode['tanggal_mulai'])) ?> - 
                                        <?= date('d/m/Y', strtotime($periode['tanggal_selesai'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($periode['status'] == 'aktif'): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php elseif ($periode['status'] == 'selesai'): ?>
                                            <span class="badge badge-secondary">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $periode['keterangan'] ?: '-' ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if ($periode['status'] != 'aktif'): ?>
                                                <a href="<?= base_url('admin/periode/set-active/' . $periode['id']) ?>" class="btn btn-success btn-sm" title="Aktifkan">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('admin/periode/edit/' . $periode['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($periode['status'] != 'aktif'): ?>
                                                <a href="<?= base_url('admin/periode/delete/' . $periode['id']) ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus periode ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (empty($periodes)): ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Belum ada periode penilaian. 
                            <a href="<?= base_url('admin/periode/create') ?>">Tambah periode baru</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
