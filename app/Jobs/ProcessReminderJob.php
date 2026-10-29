<?php

namespace App\Jobs;

use App\Models\Reminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessReminderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $reminderId
    ) {}

    public function handle(): void
    {
        try {
            $reminder = Reminder::find($this->reminderId);

            if (!$reminder || $reminder->notified_at) {
                return;
            }

            $reminder->update([
                'status' => 'proses',
            ]);
            Log::info(
                'Reminder diproses melalui Message Queue',
                [
                    'id' => $reminder->id,
                    'title' => $reminder->title,
                    'description' => $reminder->description,
                ]
            );
            $reminder->update([
                'status' => 'selesai',
                'notified_at' => now('Asia/Jakarta'),
            ]);

        } catch (Throwable $e) {
            Log::error(
                'Gagal memproses reminder melalui Message Queue',
                [
                    'reminder_id' => $this->reminderId,
                    'error' => $e->getMessage(),
                ]
            );
            throw $e;
        }
    }
}
