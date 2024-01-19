<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantsController;
use App\Http\Controllers\TennantSep22Controller;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [TenantsController::class, 'importExportView'])->name('base');
Route::post('import', [TenantsController::class, 'import'])->name('import');
Route::get('download-pdf/{batch}', [TenantsController::class, 'downloadNotice'])->name('download-pdf');
Route::get('get-pdf/{id}',[TenantsController::class, 'getPdf'])->name('get-pdf');
Route::get('download-pdf-30days/{batch}', [TenantsController::class, 'downloadNotice30Days'])->name('download-pdf-30days');
Route::get('get-pdf-30days/{id}', [TenantsController::class, 'getPdf30Days'])->name('get-pdf-30days');
Route::get('batch-code-validaion', [TenantsController::class, 'validateBatchCode'])->name('validateBatchCode');
Route::post('update-tenant-data', [TenantsController::class, 'updateTenantData'])->name('updateTenantData');
Route::get('del_all_records', [TenantsController::class, 'delAll']);

// NEW SETUP
Route::get('/SEP22', [TennantSep22Controller::class, 'importExportView'])->name('base');
Route::get('SEP22/download-pdf/{batch}', [TennantSep22Controller::class, 'downloadNotice'])->name('download-pdf');
Route::get('SEP22/get-pdf/{id}',[TennantSep22Controller::class, 'getPdf'])->name('get-pdf');
Route::get('SEP22download-pdf-30days/{batch}', [TennantSep22Controller::class, 'downloadNotice30Days'])->name('download-pdf-30days');
Route::get('SEP22/get-pdf-30days/{id}', [TennantSep22Controller::class, 'getPdf30Days'])->name('get-pdf-30days');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
