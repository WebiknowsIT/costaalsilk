<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Sellers\SellerController;

/**
 * Sellers routes.
 */
Route::prefix('sellers')->group(function () {
    /**
     * Customer management routes.
     */
    Route::controller(SellerController::class)->group(function () {
        Route::get('', 'index')->name('admin.sellers.sellers.index');

        Route::get('view/{id}', 'show')->name('admin.sellers.sellers.view');
    });
});