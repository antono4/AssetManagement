<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Routing configuration.
 *
 * Aplikasi ini berorientasi AJAX/SPA: route web menyajikan halaman utama,
 * sedangkan endpoint /api/* mengembalikan JSON untuk operasi CRUD tanpa reload.
 */

/** @var RouteCollection $routes */

// ------------------------------------------------------------------
//  Grup publik (tanpa filter auth)
// ------------------------------------------------------------------
$routes->group('', static function (RouteCollection $routes) {
    $routes->get('/',         'Auth::index');
    $routes->get('login',     'Auth::index');
    $routes->post('login',    'Auth::attempt');
    $routes->get('logout',    'Auth::logout');
    $routes->get('setup',     'Auth::setupPassword');      // one-time password fixer
    $routes->post('setup',    'Auth::setupPassword');
});

// ------------------------------------------------------------------
//  Halaman terproteksi (SPA shell) — dilindungi filter 'auth'
// ------------------------------------------------------------------
$routes->get('dashboard', 'Dashboard::index');
$routes->get('asset',     'Asset::index');
$routes->get('category',  'Category::index');
$routes->get('user',      'User::index');
$routes->get('report',    'Report::index');

// ------------------------------------------------------------------
//  API JSON (AJAX/SPA) — juga dilindungi filter 'auth'
// ------------------------------------------------------------------
$routes->group('api', static function (RouteCollection $routes) {
    // Dashboard
    $routes->get('dashboard/stats', 'Dashboard::stats');

    // Assets
    $routes->get('assets',                'Asset::list');
    $routes->get('assets/(:num)',         'Asset::show/$1');
    $routes->post('assets',               'Asset::create');
    $routes->post('assets/(:num)',        'Asset::update/$1');
    $routes->post('assets/(:num)/delete', 'Asset::delete/$1');
    $routes->get('assets/(:num)/logs',    'Asset::logs/$1');

    // Categories
    $routes->get('categories',                 'Category::list');
    $routes->post('categories',                'Category::create');
    $routes->post('categories/(:num)',         'Category::update/$1');
    $routes->post('categories/(:num)/delete',  'Category::delete/$1');

    // Users (admin only)
    $routes->get('users',                 'User::list');
    $routes->post('users',                'User::create');
    $routes->post('users/(:num)',         'User::update/$1');
    $routes->post('users/(:num)/delete',  'User::delete/$1');

    // Reports
    $routes->get('reports/assets', 'Report::assetsReport');
});
