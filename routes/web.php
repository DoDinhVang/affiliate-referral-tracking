<?php

use App\Http\Controllers\ShopifyAuthController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
})->name('home');
Route::prefix('shopify')->group(function () {
    Route::get('install', [ShopifyAuthController::class, 'install'])->name('shopify.install');
    Route::get('callback', [ShopifyAuthController::class, 'callback'])->name('shopify.callback');
});
