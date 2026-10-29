<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SchedulerController extends Controller
{
    /**
     * Menampilkan semua reminder.
     */
    public function index()
    {
        try {
            $reminders = Reminder::latest()->get();

            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Data reminder berhasil ditampilkan',
                ],
                'data' => $reminders,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Gagal mengambil data reminder', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'meta' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan pada server',
                ],
                'data' => null,
            ], 500);
        }
    }

    /**
     * Membuat reminder baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remind_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'code' => 422,
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                ],
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $reminder = Reminder::create([
                'title' => $request->title,
                'description' => $request->description,
                'remind_at' => $request->remind_at,
                'status' => 'menunggu',
                'notified_at' => null,
                'dispatched_at' => null,
            ]);

            Log::info('Reminder berhasil dibuat', [
                'id' => $reminder->id,
                'title' => $reminder->title,
                'remind_at' => $reminder->remind_at,
            ]);

            return response()->json([
                'meta' => [
                    'code' => 201,
                    'status' => 'success',
                    'message' => 'Reminder berhasil dibuat',
                ],
                'data' => $reminder,
            ], 201);

        } catch (Throwable $e) {
            Log::error('Gagal membuat reminder', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'meta' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Gagal membuat reminder',
                ],
                'data' => null,
            ], 500);
        }
    }

    /**
     * Memperbarui reminder.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'remind_at' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'code' => 422,
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                ],
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $reminder = Reminder::find($id);

            if (!$reminder) {
                return response()->json([
                    'meta' => [
                        'code' => 404,
                        'status' => 'error',
                        'message' => 'Reminder tidak ditemukan',
                    ],
                    'data' => null,
                ], 404);
            }

            $reminder->fill($request->only([
                'title',
                'description',
                'remind_at',
            ]));

            // Reset status jika waktu reminder berubah.
            if ($reminder->isDirty('remind_at')) {
                $reminder->status = 'menunggu';
                $reminder->notified_at = null;
                $reminder->dispatched_at = null;
            }

            $reminder->save();

            Log::info('Reminder berhasil diperbarui', [
                'id' => $reminder->id,
                'title' => $reminder->title,
            ]);

            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Reminder berhasil diperbarui',
                ],
                'data' => $reminder,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Gagal memperbarui reminder', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'meta' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Gagal memperbarui reminder',
                ],
                'data' => null,
            ], 500);
        }
    }
}
