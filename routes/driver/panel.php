<?php

use Illuminate\Support\Facades\Route;

Route::get('/login' , [\App\Http\Controllers\Driver\Panel\AuthController::class,'loginView'])->name('login_view');
Route::post('/login' , [App\Http\Controllers\Driver\Panel\AuthController::class ,'login'])->name('login');
Route::get('/logout' , [App\Http\Controllers\Driver\Panel\AuthController::class ,'logout'])->name('logout');
Route::get('/manifest.json' , [App\Http\Controllers\Driver\Panel\webController::class ,'manifest']);
Route::get('/{file}/{width}/{height}/icon.png' , [App\Http\Controllers\Driver\Panel\webController::class ,'icon'])->name('icon');
Route::get('/service-worker.js' , [App\Http\Controllers\Driver\Panel\webController::class ,'serviceWorker'])->name('serviceWorker');

Route::middleware(['driverPanel'])->group(function (){
    Route::get('/' , [\App\Http\Controllers\Driver\Panel\OrderController::class , 'assigned_task'])->name('dashboard');
    Route::get('orders/update/status' , [\App\Http\Controllers\AdminCustomersController::class , 'orderStatus'])->name('orders.status');
    Route::get('orders' , [\App\Http\Controllers\Driver\Panel\OrderController::class , 'history'])->name('orders.history');
});
