<?php

use App\Http\Controllers\Api\EinvoiceController;
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

Route::prefix('einvoice')->group(function () {
    // Bulk submission (PRIMARY ENDPOINT)
    Route::post('/bulk-submit', [EinvoiceController::class, 'bulkSubmit']);

    // Single submission endpoints
    Route::post('/invoice/{id}/submit', [EinvoiceController::class, 'submitSingle']);
    Route::post('/credit-note/{id}/submit', [EinvoiceController::class, 'submitSingle']);
    Route::post('/debit-note/{id}/submit', [EinvoiceController::class, 'submitSingle']);
    Route::post('/refund-note/{id}/submit', [EinvoiceController::class, 'submitSingle']);

    // Status & management
    Route::get('/{id}/status', [EinvoiceController::class, 'checkStatus']);
    Route::post('/{id}/resubmit', [EinvoiceController::class, 'resubmit']);
    Route::get('/{documentType}/{id}/history', [EinvoiceController::class, 'getHistory']);
});
