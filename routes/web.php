<?php

use App\Http\Controllers\TacheController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('tasks', [TacheController::class, 'index']);
Route::post('tasks', [TacheController::class, 'store']);
Route::get('tasks/{id}', [TacheController::class, 'show']);
Route::put('tasks/{id}', [TacheController::class, 'update']);
Route::delete('tasks/{id}', [TacheController::class, 'destroy']);
Route::get('tasks/{id}/download', [TacheController::class, 'downloadFile']);

require __DIR__.'/auth.php';
