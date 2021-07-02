<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** @var $router \Illuminate\Routing\Router */

$router->group(['namespace' => 'Web'], function () use ($router) {
    $router->get('/login', 'AdminController@loginView');
    $router->post('/login', 'AdminController@login');
    $router->post('/logout', 'AdminController@logout');
    $router->group(['middleware' => ['auth', 'user.profile', 'user.permissions', 'user.hasStores']], function () use ($router) {
        $router->get('/', 'AdminController@index');
        $router->get('/new', 'AdminController@new');
    });
});
