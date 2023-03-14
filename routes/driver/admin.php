<?php

use Illuminate\Support\Facades\Route;

Route::get('/' , [App\Http\Controllers\Driver\Admin\AuthController::class ,'loginView'])->name('login_view');
Route::post('/login' , [App\Http\Controllers\Driver\Admin\AuthController::class ,'login'])->name('login');
Route::get('/logout' , [App\Http\Controllers\Driver\Admin\AuthController::class ,'logout'])->name('logout');
Route::get('/manifest.json' , [App\Http\Controllers\Driver\Panel\webController::class ,'manifest']);

Route::middleware(['admin'])->group(function (){
    Route::get('/home' , [\App\Http\Controllers\Driver\Admin\AssignController::class , 'show'])->name('home');

    Route::resource('driver' , 'DriverController' ,['names' => 'driver']);
    Route::get('driver/print/{lang?}/{driverId?}' , [\App\Http\Controllers\Driver\Admin\DriverController::class , 'print'])->name('driver.print');

    Route::get('orders/status/{status?}' , [\App\Http\Controllers\Driver\Admin\OrderController::class , 'search'])->name('orders.search');
    Route::get('orders/assigned/history' , [\App\Http\Controllers\Driver\Admin\OrderController::class , 'assigned_history'])->name('orders.assigned_history');
    Route::get('orders/update/status' , [\App\Http\Controllers\AdminCustomersController::class , 'orderStatus'])->name('orders.status');

    Route::prefix('ajax')
        ->as('ajax.')
        ->middleware('api')
        ->group(function (){
            Route::get('driver/{driverId}' , [\App\Http\Controllers\Driver\Admin\AjaxController::class , 'driver'])->name('driver');
            Route::post('drivers' , [\App\Http\Controllers\Driver\Admin\AjaxController::class , 'searchDriver'])->name('searchDriver');
            Route::get('order/{orderId}' , [\App\Http\Controllers\Driver\Admin\AjaxController::class , 'order'])->name('order');
            Route::get('assign/{orderId}/{driverId?}' , [\App\Http\Controllers\Driver\Admin\AjaxController::class , 'assign'])->name('assign');
        });
});
