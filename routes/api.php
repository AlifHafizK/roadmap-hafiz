<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchedulerController;
use App\Http\Controllers\MessageQueueController;
use App\Http\Controllers\MonitoringController;
use Illuminate\Http\Request;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('siswa', SiswaController::class);
    Route::post('/siswa/batch', [SiswaController::class, 'batchStore']);
    Route::post('/siswa/import-excel', [
    SiswaController::class,
    'importExcel'
    ]);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('siswa', SiswaController::class);
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/reminders', [SchedulerController::class, 'index']);
Route::post('/reminders', [SchedulerController::class, 'store']);
Route::put('/reminders/{id}', [SchedulerController::class, 'update']);
Route::get('/monitoring', [
    MonitoringController::class,
    'index',
])->middleware('auth:sanctum');