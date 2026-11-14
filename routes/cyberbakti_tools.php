<?php
use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'admin','middleware'=>['auth','web','verified','admin']], function(){
    Route::get('/cyberbakti-tools','App\\Http\\Controllers\\Admin\\CyberBaktiToolsController@index')->name('admin.cyberbakti.tools');
});
