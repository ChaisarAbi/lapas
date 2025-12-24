<?= $this->extend('layouts/auth_template') ?>

<?= $this->section('content') ?>
    <p class="login-box-msg">Silakan login untuk mengakses sistem</p>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <form action="<?= base_url('auth/processLogin') ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="input-group mb-3">
            <input type="text" class="form-control" name="username" placeholder="Username" 
                   value="<?= old('username') ?>" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
        
        <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-login btn-block">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
            </div>
        </div>
    </form>
    
    <div class="mt-3 text-center">
        <p class="mb-1">
            <small>Sistem Pendukung Keputusan Pembinaan Narapidana</small>
        </p>
        <p class="mb-0">
            <small>Versi 1.0</small>
        </p>
    </div>
<?= $this->endSection() ?>
