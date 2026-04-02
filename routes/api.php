<?php

use App\Http\Controllers\Shopify\ShopifyShopController;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['shopify.auth'])->group(function () {
    Route::prefix('shopify')->group(function () {
        Route::get('shop', [ShopifyShopController::class, 'getShopInfo']);
    });
    Route::prefix('v1')->group(function () {
    });
});
