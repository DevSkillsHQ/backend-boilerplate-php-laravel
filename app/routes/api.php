<?php

use App\Http\Controllers\TransactionController;
use App\Models\Account;
use App\Models\Transaction;
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

Route::get('/ping', function () {
    return response('The service is up and running.');
});

Route::post('/amount', [TransactionController::class, 'create'])->middleware('api');
Route::get('/transaction/{transaction}', fn(Transaction $transaction) => $transaction)->whereUuid('id');
Route::get('/balance/{account}', fn(Account $account) => $account)->whereUuid('id');

Route::get('/max_transaction_volume', [TransactionController::class, 'maxTransactionVolume']);
