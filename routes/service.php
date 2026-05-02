<?php

use App\Http\Controllers\ServiceManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('service-management')->name('service.')->group(function () {
    Route::get('/invoice/status-counts', [ServiceManagementController::class, 'getStatusCounts'])->name('invoice.status-counts');
    Route::post('/invoice/update-status', [ServiceManagementController::class, 'updateStatus'])->name('invoice.update-status');
});