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
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        .logo-icon {
            font-size: 2.5rem;
            color: white;
        }
        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            letter-spacing: 1px;
        }
        .logo-subtext {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 0.25rem;
            font-weight: 300;
        }
        .login-card {
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border: none;
            overflow: hidden;
            background: white;
        }
        .login-card-body {
            padding: 2rem;
        }
        .login-box-msg {
            text-align: center;
            color: #666;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        .input-group {
            margin-bottom: 1.25rem;
        }
        .input-group .form-control {
            border: 1px solid #ddd;
            border-right: 0;
            font-size: 14px;
            padding: 0.75rem 1rem;
            height: auto;
            transition: all 0.3s;
        }
        .input-group .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.25);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-left: 0;
            color: #666;
            font-size: 14px;
            padding: 0.75rem 1rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            width: 100%;
            margin-top: 0.5rem;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #172b5c 0%, #1e3c72 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .alert {
            font-size: 13px;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .footer-text {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.85rem;
            line-height: 1.4;
        }
        .footer-text small {
            font-size: 0.8rem;
            color: #888;
        }
        .form-control::placeholder {
            color: #999;
            font-size: 13.5px;
        }
        .input-group:focus-within .input-group-text {
            border-color: #2a5298;
            color: #2a5298;
        }
    </style>
</head>
<body class="hold-transition">
    <div class="login-box">
        <div class="login-logo">
            <div class="logo-container">
                <img src="<?= base_url('logo-lapas.png') ?>" alt="Logo Lapas" style="width: 60px; height: 60px; object-fit: contain;">
            </div>
            <div class="logo-text">SPK PEMBINAAN</div>
            <div class="logo-subtext">Sistem Pendukung Keputusan</div>
        </div>
        
        <div class="card login-card">
            <div class="card-body login-card-body">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
        
        <div class="footer-text">
            <small>© <?= date('Y') ?> - Sistem Pendukung Keputusan Pembinaan Narapidana</small><br>
            <small>Versi 1.0 • Hak Cipta Dilindungi</small>
        </div>
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
