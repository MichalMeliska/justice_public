<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuthorized;
use App\Http\Middleware\OwnerAuthorized;
use App\Http\Middleware\LocalhostAuthorized;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('attendance')->controller(AttendanceController::class)->group(function() {

    Route::post('/', 'attendance');
    Route::get('/logout', 'logout');

});

Route::prefix('tools')->controller(ToolsController::class)->group(function() {

    Route::get('/rdp/{hostname}/{route}', 'rdp')->middleware(LocalhostAuthorized::class);
    Route::get('/dameware/{hostname}', 'dameware')->middleware(LocalhostAuthorized::class);
    Route::get('/folder/{hostname}/{route}', 'folder')->middleware(LocalhostAuthorized::class);
    Route::get('/email/{address}', 'email')->middleware(LocalhostAuthorized::class);
    Route::get('/registerCopy/{ComputerSID}', 'registerCopy')->middleware(AdminAuthorized::class);
    Route::get('/refresh/{route}/{sid}', 'refresh')->middleware(AdminAuthorized::class);
    Route::get('/wol/{mac}', 'wol')->middleware(AdminAuthorized::class);
    Route::get('/getInstalledPrinters/{hostname}', 'getInstalledPrinters')->middleware(AdminAuthorized::class);
    Route::post('/setDeafultPrinter', 'setDeafultPrinter')->middleware(AdminAuthorized::class);
    Route::post('/assign', 'assignUserComputer')->middleware(OwnerAuthorized::class);
    Route::get('/registerRestart/{podatelna}', 'registerRestart')->middleware(AdminAuthorized::class);

});

Route::view('/{route}', 'index')->whereIn('route', ['servers', 'schedule'])->middleware(AdminAuthorized::class);
Route::view('/{route?}', 'index')->where('route', '.*');