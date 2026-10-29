<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Reminder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MonitoringController extends Controller
{
    public function index()
    {
        try {
        // Menghitung seluruh data siswa
        $totalSiswa = Siswa::count();

        // Mengecek apakah tabel failed_jobs tersedia di database.   
        $queueError = Schema::hasTable('failed_jobs')
            ? DB::table('failed_jobs')->count()
            : 0;

        // Jumlah error scheduler
        $scheduledError = Schema::hasTable('scheduled_job_errors')
            ? DB::table('scheduled_job_errors')->count()
            : 0;

        // Total seluruh job error
        $totalJobError = $queueError + $scheduledError;

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Monitoring berhasil ditampilkan',
            ],
            'data' => [
                'siswa' => [
                    'total' => Siswa::count(),
                ],
                'job_error' => [
                    'queue' => $queueError,
                    'scheduled' => $scheduledError,
                    'total' => $totalJobError,
                ],
            ],
        ], 200);


        } catch (Throwable $e) {
            // Catat detail error di storage/logs/laravel.log
            report($e);

            return response()->json([
                'meta' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Monitoring gagal ditampilkan',
                ],
                'data' => null,
            ], 500);
        }
    }
}
