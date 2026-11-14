<?php
use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'admin','middleware'=>['auth','web','verified','admin']], function(){
    Route::get('/pw-monitor','App\\Http\\Controllers\\Admin\\PwMonitorController@index')->name('admin.pw.monitor');
    Route::get('/pw-trades','App\\Http\\Controllers\\Admin\\PwMonitorController@trades')->name('admin.pw.trades');
    Route::get('/pw-trades/export','App\\Http\\Controllers\\Admin\\PwMonitorController@export')->name('admin.pw.trades.export');
});
