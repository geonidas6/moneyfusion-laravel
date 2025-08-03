<?php

use Illuminate\Support\Facades\Route;
use Sefako\Moneyfusion\Http\Controllers\PaymentController;
use Sefako\Moneyfusion\Http\Controllers\PayoutController;
use Sefako\Moneyfusion\Http\Controllers\TransactionController;

Route::group(['middleware' => 'web', 'as' => 'moneyfusion.'], function () {
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/{transaction}/check-status', [TransactionController::class, 'checkStatus'])->name('transactions.checkStatus');
    Route::get('/payment', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

    Route::get('/payout', [PayoutController::class, 'create'])->name('payout.create');
    Route::post('/payout', [PayoutController::class, 'store'])->name('payout.store');

    Route::post('/webhook', function (Illuminate\Http\Request $request) {
        return Sefako\Moneyfusion\Facades\Moneyfusion::handleWebhook($request);
    })->name('webhook');
});
