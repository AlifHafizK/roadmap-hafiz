<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class MonitoringController extends Controller
{
    public function index()
    {
        try {
            // Menghitung seluruh data siswa
            $totalSiswa = Siswa::count();

            // Menghitung queue error
            $queueError = Schema::hasTable('failed_jobs')
                ? DB::table('failed_jobs')->count()
                : 0;

            // Menghitung scheduled error
            $scheduledError = Schema::hasTable('scheduled_job_errors')
                ? DB::table('scheduled_job_errors')->count()
                : 0;

            // Menghitung seluruh job error
            $totalJobError = $queueError + $scheduledError;

            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Monitoring berhasil ditampilkan',
                ],
                'data' => [
                    'siswa' => [
                        'total' => $totalSiswa,
                    ],
                    'job_error' => [
                        'queue' => $queueError,
                        'scheduled' => $scheduledError,
                        'total' => $totalJobError,
                    ],
                ],
            ], 200);

        } catch (Throwable $e) {
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