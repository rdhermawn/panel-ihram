<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'verified', 'admin']], static function () {
    Route::get('cyberbakti-features', [\App\Http\Controllers\Admin\CyberBaktiFeatureController::class, 'index'])
        ->name('admin.cyberbakti.features');
});
