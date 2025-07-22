<?php

use Illuminate\Support\Facades\Route;
use Vendor\MoneyFusion\Http\Controllers\MoneyFusionController;
use Vendor\MoneyFusion\Http\Controllers\InvoiceController; // N'oubliez pas d'importer ce contrôleur

Route::group(['prefix' => config('moneyfusion.routes.prefix'), 'middleware' => config('moneyfusion.routes.middleware')], function () {
    // Routes de paiement existantes
    Route::get('/', [MoneyFusionController::class, 'showPaymentForm'])->name('moneyfusion.form');
    Route::post('/pay', [MoneyFusionController::class, 'processPayment'])->name('moneyfusion.pay');
    Route::get('/' . config('moneyfusion.routes.callback_path'), [MoneyFusionController::class, 'handleCallback'])->name('moneyfusion.callback');

    // Nouvelles routes pour les factures
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('moneyfusion.invoices.index');
    Route::get('/invoices/{transaction}', [InvoiceController::class, 'show'])->name('moneyfusion.invoices.show');
    Route::get('/invoices/{transaction}/download', [InvoiceController::class, 'download'])->name('moneyfusion.invoices.download');
});

Route::post('/' . config('moneyfusion.routes.prefix') . '/' . config('moneyfusion.routes.webhook_path'), [MoneyFusionController::class, 'handleWebhook'])->name('moneyfusion.webhook');


// Route pour les webhooks (avec middleware de vérification de signature)
Route::post('/' . config('moneyfusion.routes.prefix') . '/' . config('moneyfusion.routes.webhook_path'), [MoneyFusionController::class, 'handleWebhook'])
    ->middleware(\Vendor\MoneyFusion\Http\Middleware\VerifyMoneyFusionWebhookSignature::class) // Ajoutez ceci
    ->name('moneyfusion.webhook');