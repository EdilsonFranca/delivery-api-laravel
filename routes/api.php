<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdditionalController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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
date_default_timezone_set("America/Sao_Paulo");


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login'] );
Route::post('register', [AuthController::class, 'register']);
Route::get('/gerUser', [AuthController::class, 'gerUser'] );



Route::post('password/email',  [AuthController::class, 'forgot']);
Route::post('password/reset',  [AuthController::class, 'reset']);

Route::prefix('product')->group(function()
{
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/{id}/edit', [ProductController::class, 'edit']);
    Route::get('/price_promotion', [HomeController::class, 'index']);

    Route::post('/', [ProductController::class, 'create']);
    Route::post('/{id}', [ProductController::class, 'update']);

    Route::delete('/{id}', [ProductController::class, 'destroy']);
});

Route::prefix('category')->group(function()
{
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('with_product', [CategoryController::class, 'categoryWithProduct']);
    Route::post('/', [CategoryController::class, 'create']);
    Route::post('/add_additional', [CategoryController::class, 'add_additional']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
    Route::get('/{id}', [CategoryController::class, 'show']);
});

Route::prefix('additional')->group(function()
{
    Route::get('/', [AdditionalController::class, 'index']);
    Route::post('/', [AdditionalController::class, 'create']);
    Route::delete('/{id}', [AdditionalController::class, 'destroy']);
});

Route::prefix('order')->group(function()
{
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::post('/', [OrderController::class, 'create']);
    Route::post('/change_status/{orderId}', [OrderController::class, 'change_status']);
});

Route::prefix('dashboard')->group(function()
{
    Route::get('/', [OrderController::class, 'dashboard_status']);
    Route::post('/change_status', [OrderController::class, 'dashboard_change_status']);
    Route::get('/order/{type}', [OrderController::class, 'dashboard']);
});



Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});