<?php

namespace App\Console\Commands;

use App\Jobs\ProcessReminderJob;
use App\Models\Reminder;
use App\Models\ScheduledJobError;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateReminderCommand extends Command
{
    protected $signature = 'reminder:update';

    protected $description = 'Update status pengingat yang sudah waktunya';

    public function handle(): int
    {
        try {
            $sekarang = Carbon::now('Asia/Jakarta');
            
            $reminders = Reminder::whereNull('notified_at')
                ->whereNull('dispatched_at')
                ->whereIn('status', ['menunggu', 'proses'])
                ->get();

            Log::info('Pemeriksaan reminder dimulai', [
                'sekarang_wib' => $sekarang->toDateTimeString(),
                'jumlah_ditemukan' => $reminders->count(),
            ]);

            foreach ($reminders as $reminder) {
                $waktuReminder = Carbon::parse(
                    $reminder->remind_at,
                    'Asia/Jakarta'
                )->setTimezone('Asia/Jakarta');

                Log::info('Memeriksa reminder', [
                    'id' => $reminder->id,
                    'remind_at' => $waktuReminder->toDateTimeString(),
                    'status' => $reminder->status,
                    'notified_at' => $reminder->notified_at,
                    'dispatched_at' => $reminder->dispatched_at,
                ]);

                if ($waktuReminder->lessThanOrEqualTo($sekarang)) {
                    $reminder->update([
                        'status' => 'proses',
                        'dispatched_at' => $sekarang,
                    ]);

                    ProcessReminderJob::dispatch($reminder->id);
                }
            }

            $this->info(
                'Pemeriksaan selesai. Jumlah reminder: '
                . $reminders->count()
            );

            return Command::SUCCESS;

        } catch (Throwable $e) {

            Log::error('Gagal update reminder', [
                'error' => $e->getMessage(),
            ]);

            try {
                ScheduledJobError::create([
                    'command' => 'reminder:update',
                    'error_message' => $e->getMessage(),
                    'failed_at' => Carbon::now('Asia/Jakarta'),
                ]);

            } catch (Throwable $dbError) {
                Log::critical('Gagal menyimpan error scheduler ke database', [
                    'error' => $dbError->getMessage(),
                ]);
            }

            $this->error('Terjadi kesalahan saat update reminder');

            return Command::FAILURE;
        }
    }
}
