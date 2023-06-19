<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TranslationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::apiResource('translation', TranslationController::class)
    ->only([
        'index',
        'show',
        'store',
        'update',
        'destroy'
    ])
    ->scoped([
        'translation' => 'key'
    ]);

