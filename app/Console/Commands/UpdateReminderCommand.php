<?php

namespace App\Console\Commands;

use App\Jobs\ProcessReminderJob;
use App\Models\Reminder;
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

            // Ambil reminder yang belum selesai dan belum dikirim ke queue.
            $reminders = Reminder::whereNull('notified_at')
                ->whereNull('dispatched_at')
                ->whereIn('status', ['menunggu', 'proses'])
                ->get();

            Log::info('Pemeriksaan reminder dimulai', [
                'sekarang_wib' => $sekarang->toDateTimeString(),
                'jumlah_ditemukan' => $reminders->count(),
            ]);

            foreach ($reminders as $reminder) {
                // Baca waktu reminder.
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

                // Jika waktu reminder sudah tiba, kirim Job ke queue.
                Log::info('Perbandingan waktu reminder', [
                    'waktu_reminder' => $waktuReminder->toIso8601String(),
                    'sekarang' => $sekarang->toIso8601String(),
                    'sudah_jatuh_tempo' => $waktuReminder->lessThanOrEqualTo($sekarang),
                ]);
                if ($waktuReminder->lessThanOrEqualTo($sekarang)) {
                    Log::info('Reminder sudah jatuh tempo', [
                        'id' => $reminder->id,
                    ]);

                    $reminder->update([
                        'status' => 'proses',
                        'dispatched_at' => $sekarang,
                    ]);

                    ProcessReminderJob::dispatch($reminder->id);

                    continue;
                }

                // Jika waktu reminder tinggal satu menit atau kurang.
                if ($waktuReminder->lessThanOrEqualTo(
                    $sekarang->copy()->addMinute()
                )) {
                    $reminder->update([
                        'status' => 'proses',
                    ]);
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

            $this->error('Terjadi kesalahan saat update reminder');

            return Command::FAILURE;
        }
    }
}
