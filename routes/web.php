<?php

use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Generate Application Key
$router->get('/key', function() {
    return Str::random(32);
});

$router->group(['prefix' => 'api/'], function() use($router) {
    $router->get('category', 'CategoryController@index');
    $router->post('category', 'CategoryController@store');
    $router->put('category/{id}', 'CategoryController@update');
    $router->delete('category/{id}', 'CategoryController@destroy');

    $router->get('customer', 'CustomerController@index');
    $router->post('customer', 'CustomerController@store');
    $router->put('customer/{id}', 'CustomerController@update');
    $router->delete('customer/{id}', 'CustomerController@destroy');

    $router->get('product', 'ProductController@index');
    $router->get('product/available/{category_id}', 'ProductController@getProductAvailable');
    $router->post('product', 'ProductController@store');
    $router->put('product/{id}', 'ProductController@update');
    $router->delete('product/{id}', 'ProductController@destroy');

    $router->get('transaction/summary', 'TransactionController@getTransactionSummary');
    $router->get('transaction/{range}', 'TransactionController@index');
    $router->post('transaction', 'TransactionController@store');
});