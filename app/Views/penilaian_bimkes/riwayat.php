<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'penilaian';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('bimkesmaswat/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Riwayat Penilaian</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Penilaian Narapidana</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('bimkesmaswat/penilaian') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Input Nilai Baru
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
                    
                    <!-- Filter Form -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="get" action="<?= base_url('bimkesmaswat/penilaian/riwayat') ?>" class="form-inline">
                                <div class="form-group mr-3 mb-2">
                                    <label for="periode" class="mr-2">Periode:</label>
                                    <select name="periode" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Periode</option>
                                        <?php foreach ($periode_list as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= $selected_periode == $key ? 'selected' : '' ?>><?= $value ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group mr-3 mb-2">
                                    <label for="narapidana_id" class="mr-2">Narapidana:</label>
                                    <select name="narapidana_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Narapidana</option>
                                        <?php foreach ($narapidana_list as $n): ?>
                                            <option value="<?= $n['id'] ?>" <?= $selected_narapidana == $n['id'] ? 'selected' : '' ?>>
                                                <?= $n['nomor_registrasi'] ?> - <?= $n['nama_lengkap'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-2">
                                    <a href="<?= base_url('bimkesmaswat/penilaian/riwayat') ?>" class="btn btn-secondary">Reset Filter</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <?php if ($selected_narapidana): ?>
                        <!-- Tampilan detail penilaian untuk narapidana tertentu -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Detail Penilaian</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($penilaian)): ?>
                                            <?php 
                                            // Group by periode
                                            $grouped_by_periode = [];
                                            foreach ($penilaian as $item) {
                                                $grouped_by_periode[$item['periode']][] = $item;
                                            }
                                            ?>
                                            
                                            <?php foreach ($grouped_by_periode as $periode => $items): ?>
                                                <div class="mb-4">
                                                    <h5 class="mb-3">
                                                        <span class="badge badge-info">Periode: <?= $periode ?></span>
                                                        <small class="text-muted ml-2"><?= count($items) ?> kriteria</small>
                                                    </h5>
                                                    
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th width="5%">No</th>
                                                                    <th width="20%">Kriteria</th>
                                                                    <th width="15%">Kode</th>
                                                                    <th width="15%">Nilai</th>
                                                                    <th width="20%">Penilai</th>
                                                                    <th width="15%">Tanggal</th>
                                                                    <th width="10%">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($items as $index => $item): ?>
                                                                <tr>
                                                                    <td><?= $index + 1 ?></td>
                                                                    <td><?= $item['kriteria_nama'] ?></td>
                                                                    <td><?= $item['kode'] ?></td>
                                                                    <td><?= number_format($item['nilai'], 2) ?></td>
                                                                    <td><?= $item['penilai_nama'] ?></td>
                                                                    <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                                                                    <td>
                                                                        <a href="<?= base_url('bimkesmaswat/penilaian/edit/' . $item['id']) ?>" class="btn btn-warning btn-sm">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> Tidak ada data penilaian untuk narapidana ini.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Tampilan daftar narapidana dengan riwayat penilaian -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Daftar Narapidana dengan Riwayat Penilaian</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($penilaian)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">No</th>
                                                            <th width="15%">Nomor Registrasi</th>
                                                            <th width="25%">Nama Lengkap</th>
                                                            <th width="15%">Total Penilaian</th>
                                                            <th width="20%">Penilaian Terakhir</th>
                                                            <th width="20%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($penilaian as $index => $item): ?>
                                                        <tr>
                                                            <td><?= $index + 1 ?></td>
                                                            <td><strong><?= $item['nomor_registrasi'] ?></strong></td>
                                                            <td><?= $item['nama_lengkap'] ?></td>
                                                            <td>
                                                                <span class="badge badge-primary"><?= $item['total_penilaian'] ?> penilaian</span>
                                                            </td>
                                                            <td>
                                                                <?php if ($item['last_penilaian']): ?>
                                                                    <?= date('d/m/Y H:i', strtotime($item['last_penilaian'])) ?>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="<?= base_url('bimkesmaswat/penilaian/riwayat?narapidana_id=' . $item['narapidana_id'] . ($selected_periode ? '&periode=' . $selected_periode : '')) ?>" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-eye"></i> Lihat Riwayat
                                                                </a>
                                                                <a href="<?= base_url('bimkesmaswat/penilaian?narapidana_id=' . $item['narapidana_id']) ?>" class="btn btn-success btn-sm">
                                                                    <i class="fas fa-plus"></i> Input Nilai
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> Tidak ada data riwayat penilaian.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
