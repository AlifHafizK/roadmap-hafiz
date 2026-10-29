<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\KirimPesanJob;

class MessageQueueController extends Controller
{
    public function kirimPesan(Request $request)
    {
        $request->validate([
            'pesan' => 'required|string|max:255',
        ]);

        KirimPesanJob::dispatch();

        return response()->json([
            'message' => 'Pesan berhasil dimasukkan ke antrean!',
            'pesan' => $request->pesan,
        ], 202);
    }
}
