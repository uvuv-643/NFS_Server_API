<?php

use App\Http\Controllers\FSController;
use App\Http\Controllers\UserTokenController;
use Illuminate\Support\Facades\Route;

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

Route::get('/list', [FSController::class, 'list']);
Route::get('/create', [FSController::class, 'create']);
Route::get('/read', [FSController::class, 'read']);
Route::get('/write', [FSController::class, 'write']);
Route::get('/link', [FSController::class, 'link']);
Route::get('/unlink', [FSController::class, 'unlink']);
Route::get('/rmdir', [FSController::class, 'rmdir']);
Route::get('/lookup', [FSController::class, 'lookup']);

// b4b18a03-c9a4-4b50-b979-a91760658168
Route::get('/token', [UserTokenController::class, 'store']);
