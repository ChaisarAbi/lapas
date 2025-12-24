<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SPK Pembinaan Narapidana' ?></title>
    
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AdminLTE3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <style>
        :root {
            --primary-color: #1e3c72;
            --secondary-color: #2a5298;
            --accent-color: #4a6fc1;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-color: #2d3748;
            --text-light: #718096;
            --success-color: #38a169;
            --warning-color: #d69e2e;
            --danger-color: #e53e3e;
            --info-color: #3182ce;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-bg);
        }
        
        .main-sidebar {
            background-color: var(--primary-color);
        }
        .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.1);
        }
        .brand-link img {
            opacity: 0.9;
        }
        .brand-text {
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .content-wrapper {
            background-color: var(--light-bg);
        }
        .user-panel {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* ===== TYPOGRAPHY IMPROVEMENTS ===== */
        h1, h2, h3, h4, h5, h6 {
            margin-bottom: 0.75rem;
            font-weight: 600;
            line-height: 1.3;
            color: var(--primary-color);
        }
        
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
        }
        
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        /* Text color improvements */
        body {
            color: #2d3748;
        }
        
        .text-muted {
            color: #718096 !important;
        }
        
        .text-dark {
            color: #2d3748 !important;
        }
        
        .text-light {
            color: #a0aec0 !important;
        }
        
        /* ===== TABLE IMPROVEMENTS ===== */
        .table {
            font-size: 13px;
            border-collapse: separate;
            border-spacing: 0;
            background-color: white;
        }
        
        .table th {
            font-weight: 600;
            font-size: 13px;
            background-color: #f1f5f9;
            border-top: 1px solid var(--border-color);
            border-bottom: 2px solid var(--border-color);
            color: var(--primary-color);
            padding: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
        }
        
        .table td {
            padding: 0.75rem;
            border-top: 1px solid var(--border-color);
            vertical-align: middle;
            color: #4a5568;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(241, 245, 249, 0.5);
        }
        
        .table-bordered {
            border: 1px solid var(--border-color);
        }
        
        .table-bordered th,
        .table-bordered td {
            border: 1px solid var(--border-color);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(226, 232, 240, 0.3);
        }
        
        /* ===== BUTTON IMPROVEMENTS ===== */
        .btn {
            font-size: 13px;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(30, 60, 114, 0.2);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }
        
        /* ===== FORM IMPROVEMENTS ===== */
        .form-control {
            font-size: 13px;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            transition: border-color 0.2s;
            background-color: #fff;
            color: #4a5568;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 111, 193, 0.25);
            background-color: #fff;
        }
        
        .form-control::placeholder {
            color: #a0aec0;
            font-size: 12.5px;
        }
        
        /* Form layout improvements */
        .form-row {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }
        
        .form-row > .col,
        .form-row > [class*="col-"] {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        /* Required field indicator */
        .required::after {
            content: " *";
            color: #e53e3e;
        }
        
        /* Form group spacing */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group:last-child {
            margin-bottom: 0;
        }
        
        /* Form labels */
        label {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 0.35rem;
            color: #4a5568;
        }
        
        /* Help text */
        .form-text {
            font-size: 12px;
            color: #718096;
            margin-top: 0.25rem;
        }
        
        /* ===== CARD IMPROVEMENTS ===== */
        .card {
            margin-bottom: 1.5rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.2s;
            background-color: #fff;
        }
        
        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-header {
            padding: 1rem 1.5rem;
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            color: var(--primary-color);
            border-radius: 8px 8px 0 0 !important;
        }
        
        .card-footer {
            padding: 1rem 1.5rem;
            background-color: #f8fafc;
            border-top: 1px solid var(--border-color);
            border-radius: 0 0 8px 8px !important;
        }
        
        /* Info-box improvements (small-box replacement) */
        .small-box {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.2s;
        }
        
        .small-box:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .small-box .icon {
            font-size: 2.5rem;
            opacity: 0.9;
        }
        
        .small-box .inner h3 {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .small-box .inner p {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .small-box .small-box-footer {
            background-color: rgba(0, 0, 0, 0.05);
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.8125rem;
            font-weight: 500;
        }
        
        .small-box .small-box-footer:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        /* Report card */
        .report-card {
            border-top: 3px solid var(--accent-color);
        }
        
        /* ===== NAVIGATION IMPROVEMENTS ===== */
        .nav-link {
            font-size: 13px;
            transition: all 0.2s;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
        }
        
        /* ===== BREADCRUMB IMPROVEMENTS ===== */
        .breadcrumb {
            font-size: 13px;
            margin-bottom: 0;
            background-color: transparent;
            padding: 0.5rem 0;
        }
        
        .breadcrumb-item a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .breadcrumb-item.active {
            color: var(--text-light);
            font-weight: 500;
        }
        
        /* ===== ALERT IMPROVEMENTS ===== */
        .alert {
            font-size: 13px;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            border: none;
        }
        
        .alert-success {
            background-color: #c6f6d5;
            color: #22543d;
        }
        
        .alert-warning {
            background-color: #feebc8;
            color: #744210;
        }
        
        .alert-danger {
            background-color: #fed7d7;
            color: #742a2a;
        }
        
        .alert-info {
            background-color: #bee3f8;
            color: #2a4365;
        }
        
        /* ===== BADGE IMPROVEMENTS ===== */
        .badge {
            font-size: 11px;
            padding: 0.35em 0.65em;
            font-weight: 600;
            border-radius: 10px;
        }
        
        .badge-primary {
            background-color: var(--primary-color);
        }
        
        .badge-success {
            background-color: var(--success-color);
        }
        
        .badge-warning {
            background-color: var(--warning-color);
        }
        
        .badge-danger {
            background-color: var(--danger-color);
        }
        
        /* ===== INFO-BOX IMPROVEMENTS ===== */
        .info-box {
            margin-bottom: 1.25rem;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            background-color: white;
        }
        
        .info-box-text {
            font-size: 13px;
            font-weight: 500;
            color: #4a5568;
        }
        
        .info-box-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        /* ===== MODAL IMPROVEMENTS ===== */
        .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .modal-header {
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
        }
        
        /* ===== PAGINATION IMPROVEMENTS ===== */
        .pagination {
            font-size: 13px;
        }
        
        .page-link {
            color: var(--accent-color);
            border-color: var(--border-color);
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* ===== CONTENT HEADER IMPROVEMENTS ===== */
        .content-header h1 {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        /* ===== MAIN FOOTER IMPROVEMENTS ===== */
        .main-footer {
            background-color: white;
            border-top: 1px solid var(--border-color);
            padding: 1rem;
            font-size: 13px;
            color: var(--text-light);
        }
        
        /* ===== CUSTOM SCROLLBAR ===== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* ===== DASHBOARD LAYOUT SPECIFIC ===== */
        .dashboard-stats .card {
            height: 100%;
        }
        
        .dashboard-chart {
            height: 300px;
        }
        
        /* Small-box color variants */
        .small-box.bg-info {
            background-color: var(--info-color) !important;
        }
        
        .small-box.bg-success {
            background-color: var(--success-color) !important;
        }
        
        .small-box.bg-warning {
            background-color: var(--warning-color) !important;
        }
        
        .small-box.bg-danger {
            background-color: var(--danger-color) !important;
        }
        
        .small-box.bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        /* ===== REPORT LAYOUT SPECIFIC ===== */
        .report-header {
            background-color: #f8fafc;
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }
        
        .report-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .report-subtitle {
            font-size: 1rem;
            color: var(--text-light);
            margin-bottom: 0;
        }
        
        .report-section {
            margin-bottom: 2rem;
        }
        
        .report-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        /* ===== PRINT STYLES ===== */
        @media print {
            .no-print {
                display: none !important;
            }
            
            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }
            
            .table {
                font-size: 12px;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="<?= base_url($dashboard_url ?? 'admin/dashboard') ?>" class="nav-link">Dashboard</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-user mr-2"></i> <?= session()->get('username') ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="<?= base_url('logout') ?>" class="dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <?php
        // Determine which sidebar to include based on user role
        $role = session()->get('role');
        $sidebarFile = '';
        
        switch ($role) {
            case 'TPP':
                $sidebarFile = 'tpp_sidebar.php';
                $dashboard_url = 'tpp/dashboard';
                break;
            case 'ADMIN':
                $sidebarFile = 'admin_sidebar.php';
                $dashboard_url = 'admin/dashboard';
                break;
            case 'BIMKEMASWAT':
                $sidebarFile = 'bimkesmaswat_sidebar.php';
                $dashboard_url = 'bimkesmaswat/dashboard';
                break;
            case 'WALI_PEMASYARAKATAN':
                $sidebarFile = 'wali_sidebar.php';
                $dashboard_url = 'wali/dashboard';
                break;
            case 'KEPALA_LAPAS':
                $sidebarFile = 'kalapas_sidebar.php';
                $dashboard_url = 'kalapas/dashboard';
                break;
            default:
                $sidebarFile = 'tpp_sidebar.php';
                $dashboard_url = 'tpp/dashboard';
        }
        
        // Include the sidebar file
        if (file_exists(APPPATH . 'Views/layouts/sidebars/' . $sidebarFile)) {
            echo view('layouts/sidebars/' . $sidebarFile);
        } else {
            // Fallback to default sidebar
            ?>
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="<?= base_url($dashboard_url ?? 'admin/dashboard') ?>" class="brand-link text-center">
                    <span class="brand-text font-weight-light"><b>SPK</b> Pembinaan</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <i class="fas fa-user-circle img-circle elevation-2" style="font-size: 2rem; color: #fff;"></i>
                        </div>
                        <div class="info">
                            <a href="#" class="d-block"><?= session()->get('nama_lengkap') ?? session()->get('username') ?></a>
                            <small class="text-light"><?= session()->get('role') ?></small>
                        </div>
                    </div>

                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <?= $this->renderSection('sidebar_menu') ?>
                        </ul>
                    </nav>
                </div>
            </aside>
            <?php
        }
        ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $page_title ?? 'Dashboard' ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?= $this->renderSection('breadcrumb') ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </div>

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Versi</b> 1.0
            </div>
            <strong>Copyright &copy; <?= date('Y') ?> SPK Pembinaan Narapidana.</strong> All rights reserved.
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <!-- Bootstrap 4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <?php if (session()->getFlashdata('error')): ?>
    <script>
        $(document).ready(function() {
            toastr.error('<?= session()->getFlashdata('error') ?>', 'Error');
        });
    </script>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('success')): ?>
    <script>
        $(document).ready(function() {
            toastr.success('<?= session()->getFlashdata('success') ?>', 'Sukses');
        });
    </script>
    <?php endif; ?>
</body>
</html>
