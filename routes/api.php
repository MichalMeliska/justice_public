<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Middleware\OwnerAuthorized;
use App\Http\Middleware\AdminAuthorized;

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

Route::controller(ApiController::class)->group(function() {

    Route::get('/getRefreshData', 'getRefreshData');
    Route::get('/getRegisterVersionServer', 'getRegisterVersionServer');
    Route::get('/getPrinters', 'getPrinters');
    Route::post('/phonebookImport', 'phonebookImport')->middleware(AdminAuthorized::class);
    Route::post('/phonebookChanges', 'phonebookChanges')->middleware(OwnerAuthorized::class);
    Route::post('/scheduleImport', 'scheduleImport')->middleware(AdminAuthorized::class);
    Route::get('/getIP', 'getIP')->middleware(AdminAuthorized::class);

});