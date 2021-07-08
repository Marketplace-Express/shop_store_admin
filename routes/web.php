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
    $router->post('/logout', 'AdminController@logout')->name('logout');
    $router->get('/logout', 'AdminController@logout')->name('logout');
    $router->group(['middleware' => ['auth', 'user.profile', 'user.hasStores']], function () use ($router) {
        $router->get('/stores', 'StoreController@getStores');
        $router->get('/manageStore/{storeId}', 'StoreController@manageStore')->name('manage_store');
        $router->get('/changeStore', 'StoreController@changeStore')->name('change_store');
        $router->group(['middleware' => ['store.isSet', 'user.permissions']], function () use($router) {
            $router->get('/', 'AdminController@index')->name('dashboard');
            $router->get('/categories', 'CategoriesController@listView')->name('categories_list');
        });
    });
});
