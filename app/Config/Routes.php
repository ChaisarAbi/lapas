<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Authentication Routes
$routes->get('/login', 'AuthController::login');
$routes->post('/auth/processLogin', 'AuthController::processLogin');
$routes->get('/logout', 'AuthController::logout');

// Admin Routes
$routes->group('admin', ['filter' => 'role:ADMIN'], function($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
    
    // User Management
    $routes->get('users', 'UserController::index');
    $routes->get('users/create', 'UserController::create');
    $routes->post('users/store', 'UserController::store');
    $routes->get('users/edit/(:num)', 'UserController::edit/$1');
    $routes->post('users/update/(:num)', 'UserController::update/$1');
    $routes->get('users/delete/(:num)', 'UserController::delete/$1');
    
    // Narapidana Management
    $routes->get('narapidana', 'NarapidanaController::index');
    $routes->get('narapidana/create', 'NarapidanaController::create');
    $routes->post('narapidana/store', 'NarapidanaController::store');
    $routes->get('narapidana/edit/(:num)', 'NarapidanaController::edit/$1');
    $routes->post('narapidana/update/(:num)', 'NarapidanaController::update/$1');
    $routes->get('narapidana/delete/(:num)', 'NarapidanaController::delete/$1');
    
    // Perhitungan TOPSIS
    $routes->get('perhitungan/topsis', 'PerhitunganController::topsis');
    $routes->get('perhitungan/cetak', 'PerhitunganController::cetakLaporan');
    
    // Laporan Management
    $routes->get('laporan', 'LaporanController::index');
    $routes->get('laporan/preview-ranking', 'LaporanController::previewRanking');
    $routes->get('laporan/cetak-ranking', 'LaporanController::cetakRanking');
    // Route untuk laporan validasi dan penilaian petugas dihapus sesuai permintaan user
});

// TPP Routes
$routes->group('tpp', ['filter' => 'role:TPP'], function($routes) {
    $routes->get('dashboard', 'TppController::dashboard');
    
    // Kriteria Management
    $routes->get('kriteria', 'KriteriaController::index');
    $routes->get('kriteria/create', 'KriteriaController::create');
    $routes->post('kriteria/store', 'KriteriaController::store');
    $routes->get('kriteria/edit/(:num)', 'KriteriaController::edit/$1');
    $routes->post('kriteria/update/(:num)', 'KriteriaController::update/$1');
    $routes->get('kriteria/delete/(:num)', 'KriteriaController::delete/$1');
    
    // Subkriteria Management
    $routes->get('subkriteria', 'SubkriteriaController::index');
    $routes->get('subkriteria/create', 'SubkriteriaController::create');
    $routes->post('subkriteria/store', 'SubkriteriaController::store');
    $routes->get('subkriteria/edit/(:num)', 'SubkriteriaController::edit/$1');
    $routes->post('subkriteria/update/(:num)', 'SubkriteriaController::update/$1');
    $routes->get('subkriteria/delete/(:num)', 'SubkriteriaController::delete/$1');
    $routes->get('subkriteria/by/(:num)', 'SubkriteriaController::byKriteria/$1');
    $routes->post('subkriteria/update-bobot', 'SubkriteriaController::updateBobot');
    
    // Input Bobot dan ANP
    $routes->get('bobot', 'TppBobotController::index');
    $routes->post('bobot/simpan', 'TppBobotController::simpan');
    $routes->get('bobot/matriks', 'TppBobotController::matriksPerbandingan');
    $routes->post('bobot/simpan-matriks', 'TppBobotController::simpanMatriks');
    $routes->get('bobot/konsistensi', 'TppBobotController::konsistensi');
    
    // Hasil ANP
    $routes->get('anp', 'TppAnpController::index');
    $routes->post('anp/simpan-bobot', 'TppAnpController::simpanBobotAkhir');
    // Route cetak ANP dihapus karena file view tidak ada
    
    // Periode Penilaian
    $routes->get('periode', 'TppPeriodeController::index');
    $routes->get('periode/create', 'TppPeriodeController::create');
    $routes->post('periode/store', 'TppPeriodeController::store');
    $routes->get('periode/edit/(:num)', 'TppPeriodeController::edit/$1');
    $routes->post('periode/update/(:num)', 'TppPeriodeController::update/$1');
    $routes->get('periode/delete/(:num)', 'TppPeriodeController::delete/$1');
    $routes->get('periode/set-active/(:num)', 'TppPeriodeController::setActive/$1');
});

// BIMKEMASWAT Routes
$routes->group('bimkesmaswat', ['filter' => 'role:BIMKEMASWAT'], function($routes) {
    $routes->get('dashboard', 'BimkesmaswatController::dashboard');
    
    // Penilaian Management
    $routes->get('penilaian', 'PenilaianBimkesController::index');
    $routes->post('penilaian/save', 'PenilaianBimkesController::save');
    $routes->get('penilaian/riwayat', 'PenilaianBimkesController::riwayat');
    $routes->get('penilaian/edit/(:num)', 'PenilaianBimkesController::edit/$1');
    $routes->post('penilaian/update/(:num)', 'PenilaianBimkesController::update/$1');
});

// Wali Pemasyarakatan Routes
$routes->group('wali', ['filter' => 'role:WALI_PEMASYARAKATAN'], function($routes) {
    $routes->get('dashboard', 'WaliController::dashboard');
    
    // Hasil Penilaian
    $routes->get('hasil', 'WaliController::hasil');
    
    // Ranking
    $routes->get('ranking', 'RankingController::index');
    $routes->get('ranking/detail/(:num)', 'RankingController::detail/$1');
});

    // Kepala Lapas Routes
    $routes->group('kalapas', ['filter' => 'role:KEPALA_LAPAS'], function($routes) {
        $routes->get('dashboard', 'KalapasController::dashboard');
        
        // Hasil Penilaian
        $routes->get('hasil', 'KalapasController::hasil');
        
        // Validasi
        $routes->get('validasi', 'KalapasController::validasi');
        $routes->post('validasi/simpan', 'KalapasController::simpanValidasi');
        $routes->get('hasil-validasi', 'KalapasController::hasilValidasi');
        $routes->get('riwayat-validasi', 'KalapasController::riwayatValidasi');
        
        // Ranking
        $routes->get('ranking', 'RankingController::index');
        $routes->get('ranking/detail/(:num)', 'RankingController::detail/$1');
        $routes->get('ranking/cetak', 'RankingController::cetakLaporan');
        
        // Cetak Laporan dengan Preview
        $routes->get('preview-cetak', 'KalapasController::previewCetak');
        $routes->get('cetak-laporan', 'KalapasController::cetakLaporan');
    });
