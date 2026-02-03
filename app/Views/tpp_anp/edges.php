<?= $this->extend('layouts/dashboard_template') ?>

<?php
// Set active menu untuk sidebar
$activeMenu = 'edges';
?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('tpp/anp') ?>">ANP</a></li>
    <li class="breadcrumb-item active">Kelola Edges</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Kelola Edges ANP
                    </h3>
                    <div class="card-tools">
                        <?php if ($periode && isset($periode['nama'])): ?>
                            <span class="badge badge-primary">Periode: <?= esc($periode['nama']) ?></span>
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
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Edges (panah)</strong> dalam ANP menunjukkan pengaruh antar node. 
                                Pilih <strong>From Node</strong> dan centang <strong>To Nodes</strong> yang dipengaruhinya.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-arrow-right mr-2"></i>
                                        Pilih From Node
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="<?= base_url('/tpp/anp/edges') ?>">
                                        <div class="form-group">
                                            <label>From Node</label>
                                            <select name="from_node_id" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- Pilih From Node --</option>
                                                <?php foreach ($subkriteria as $node): ?>
                                                    <option value="<?= $node['id'] ?>" <?= ($selected_from && $selected_from['id'] == $node['id']) ? 'selected' : '' ?>>
                                                        <?= $node['kode'] ?> - <?= $node['nama'] ?>
                                                        (<?= $node['kriteria_nama'] ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </form>

                                    <?php if ($selected_from): ?>
                                        <div class="alert alert-primary mt-3">
                                            <h6>From Node Terpilih:</h6>
                                            <p class="mb-1">
                                                <strong><?= $selected_from['kode'] ?> - <?= $selected_from['nama'] ?></strong>
                                                <span class="badge badge-secondary"><?= $selected_from['kriteria_nama'] ?></span>
                                            </p>
                                            <p class="mb-0"><small>ID: <?= $selected_from['id'] ?></small></p>
                                        </div>

                                        <div class="mt-3">
                                            <h6>Edges Existing:</h6>
                                            <?php if (!empty($existing_edges)): ?>
                                                <div class="list-group">
                                                    <?php foreach ($existing_edges as $edge): ?>
                                                        <div class="list-group-item list-group-item-success">
                                                            <i class="fas fa-arrow-right text-success mr-2"></i>
                                                            To Node ID: <?= $edge['to_node_id'] ?>
                                                            <small class="text-muted ml-2">(<?= date('d/m/Y H:i', strtotime($edge['created_at'])) ?>)</small>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    Belum ada edges untuk from node ini.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Pilih from node terlebih dahulu untuk mengelola edges.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Pilih To Nodes
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($selected_from): ?>
                                        <form method="POST" action="<?= base_url('/tpp/anp/simpan-edges') ?>">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="from_node_id" value="<?= $selected_from['id'] ?>">
                                            
                                            <div class="form-group">
                                                <label>Pilih To Nodes yang dipengaruhi:</label>
                                                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                                    <?php foreach ($subkriteria as $node): ?>
                                                        <?php if ($node['id'] != $selected_from['id']): ?>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" 
                                                                       name="to_node_ids[]" value="<?= $node['id'] ?>"
                                                                       id="node_<?= $node['id'] ?>"
                                                                       <?= in_array($node['id'], array_column($existing_edges, 'to_node_id')) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="node_<?= $node['id'] ?>">
                                                                    <strong><?= $node['kode'] ?></strong> - <?= $node['nama'] ?>
                                                                    <span class="badge badge-info ml-2"><?= $node['kriteria_nama'] ?></span>
                                                                </label>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                <strong>Perhatian:</strong> Centang semua node yang dipengaruhi oleh from node terpilih.
                                            </div>
                                            
                                            <div class="text-right">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save mr-2"></i>
                                                    Simpan Edges
                                                </button>
                                                <a href="<?= base_url('/tpp/anp/pairwise-target') ?>" class="btn btn-success">
                                                    <i class="fas fa-arrow-right mr-2"></i>
                                                    Lanjut ke Pairwise Comparison
                                                </a>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Pilih from node terlebih dahulu untuk memilih to nodes.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Informasi Edges
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Konsep Edges dalam ANP:</h6>
                                            <ul>
                                                <li><strong>From Node</strong>: Node yang mempengaruhi (influencer)</li>
                                                <li><strong>To Node</strong>: Node yang dipengaruhi (target)</li>
                                                <li>Edges menunjukkan hubungan interdependensi antar node</li>
                                                <li>Pairwise comparison hanya dilakukan antar influencer nodes untuk target yang sama</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Statistik:</h6>
                                            <ul>
                                                <li>Total Nodes: <strong><?= count($subkriteria) ?></strong></li>
                                                <li>Total Edges: <strong><?= count($all_edges ?? []) ?></strong></li>
                                                <?php if ($selected_from): ?>
                                                    <li>Edges dari node ini: <strong><?= count($existing_edges) ?></strong></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Edges menentukan hubungan interdependensi dalam Analytic Network Process (ANP). 
                        Setelah mengatur edges, lanjutkan ke pairwise comparison untuk target yang dipilih.
                    </p>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
