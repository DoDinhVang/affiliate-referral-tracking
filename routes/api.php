<?php

use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('shop')->group(function () {
    Route::get('/access-token', [ShopController::class, 'getShopifyAccessToken']);
    Route::get('/shopify-products', [ShopController::class, 'getShopifyProducts']);
    Route::get('/access-scopes', [ShopController::class, 'getShopifyAccessScopes']);
});
