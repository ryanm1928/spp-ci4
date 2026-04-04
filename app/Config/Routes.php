<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', function () {
    helper('auth');

    if (!logged_in()) {
        return redirect()->to('/login');
    }

    if (in_groups('admin')) {
        return redirect()->to('/admin/dashboard');
    }

    if (in_groups('kepala_sekolah')) {
        return redirect()->to('/kepala-sekolah/dashboard');
    }

    if (in_groups('guru')) {
        return redirect()->to('/guru/dashboard');
    }

    return redirect()->to('/logout');
});

$routes->get('login', 'AuthController::login', ['as' => 'login']);
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', 'AdminController::index');

    // user management
    $routes->get('users', 'AdminUserController::index');
    $routes->get('users/create', 'AdminUserController::create');
    $routes->post('users/store', 'AdminUserController::store');
    $routes->get('users/edit/(:num)', 'AdminUserController::edit/$1');
    $routes->post('users/update/(:num)', 'AdminUserController::update/$1');
    $routes->post('users/delete/(:num)', 'AdminUserController::delete/$1');

    // kelas & tahun ajaran
    $routes->get('manage-class', 'AdminKelasTahunAjaranController::index');
    $routes->post('manage-class/kelas/store', 'AdminKelasTahunAjaranController::storeKelas');
    $routes->post('manage-class/kelas/update/(:num)', 'AdminKelasTahunAjaranController::updateKelas/$1');
    $routes->post('manage-class/kelas/delete/(:num)', 'AdminKelasTahunAjaranController::deleteKelas/$1');
    $routes->post('manage-class/tahun-ajaran/store', 'AdminKelasTahunAjaranController::storeTahunAjaran');
    $routes->post('manage-class/tahun-ajaran/update/(:num)', 'AdminKelasTahunAjaranController::updateTahunAjaran/$1');
    $routes->post('manage-class/tahun-ajaran/delete/(:num)', 'AdminKelasTahunAjaranController::deleteTahunAjaran/$1');

    // siswa
    $routes->get('siswa', 'AdminSiswaController::index');
    $routes->get('siswa/create', 'AdminSiswaController::create');
    $routes->post('siswa/store', 'AdminSiswaController::store');
    $routes->get('siswa/edit/(:num)', 'AdminSiswaController::edit/$1');
    $routes->post('siswa/update/(:num)', 'AdminSiswaController::update/$1');
    $routes->post('siswa/delete/(:num)', 'AdminSiswaController::delete/$1');
    $routes->post('siswa/bulk-delete', 'AdminSiswaController::bulkDelete');


    $routes->post('siswa/bulk-activate', 'AdminSiswaController::bulkActivate');
    $routes->post('siswa/bulk-deactivate', 'AdminSiswaController::bulkDeactivate');
});

$routes->group('kepala-sekolah', ['filter' => 'role:kepala_sekolah'], function ($routes) {
    $routes->get('dashboard', 'KepalaSekolahController::index');

    $routes->get('laporan', 'LaporanKepalaSekolahController::index');
    $routes->get('laporan/export', 'LaporanKepalaSekolahController::export');
});

$routes->group('guru', ['filter' => 'role:guru'], function ($routes) {
    $routes->get('dashboard', 'GuruController::index');

    $routes->get('spp', 'SppController::index');
    $routes->post('spp/store', 'SppController::store');

    $routes->post('spp/update/(:num)', 'SppController::update/$1');
    $routes->post('spp/undo/(:num)', 'SppController::undo/$1');

    $routes->post('spp/delete/(:num)', 'SppController::deletePayment/$1');

    $routes->get('laporan', 'LaporanGuruController::index');
    $routes->get('laporan/export', 'LaporanGuruController::export');

    $routes->get('spp/send-whatsapp/(:num)', 'SppController::sendWhatsapp/$1');
    $routes->get('spp/confirm-whatsapp-sent/(:num)', 'SppController::confirmWhatsappSent/$1');
});
