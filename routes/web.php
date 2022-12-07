<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Product_mysqlController;

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

Route::get('/', function () {
    return view('products');
    });
Route::apiResource('products', ProductController::class);

Route::get('/products-mysql', function () {
    return view('productsmysql');
    });
Route::apiResource('productsmysql', Product_mysqlController::class);
