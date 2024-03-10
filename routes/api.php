<?php

use App\Http\Controllers\BlockChainController;
use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('file', FileController::class)->only(['store']);
Route::get('full_blockchain', [BlockChainController::class, 'getFullBlockChain']);
Route::post('verify_transaction', [BlockChainController::class, 'verifyTransaction']);
Route::post('verify_block', [BlockChainController::class, 'addBlockToBlockChain']);