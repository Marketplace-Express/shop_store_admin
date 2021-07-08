<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * @var \Illuminate\Routing\Router $router
 */
$router->group(['namespace' => 'Api'], function () use ($router) {
    $router->group([], function () use ($router) {
        $router->get('/categories/fetch', 'CategoryController@fetch')->name('fetch_categories_api');
        $router->post('/categories/updateOrder', 'CategoryController@updateOrder')->name('update_categories_order');
    });
});