<?php

namespace App\Http\Controllers;

use App\Imports\SiswaImport;
use App\Http\Resources\SiswaResource;
use App\Handlers\SiswaHandler;
use App\Repositories\SiswaRepository;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Cache;
use Throwable;

class SiswaController extends Controller
{
    // MENAMPILKAN SEMUA SISWA
    public function index()
    {
        try {
            $fromCache = Cache::store('redis')->has('siswa');

            $siswa = Cache::store('redis')->remember('siswa', 60, function () {
                return Siswa::all();
            });

            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Data siswa berhasil ditampilkan'
                ],
                'source' => $fromCache ? 'cache' : 'database',
                'data' => $siswa
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'meta' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Data siswa gagal ditampilkan'
                ]
            ], 500);
        }
    }


    // MENAMBAHKAN SISWA
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nis' => 'required|string|max:30|unique:siswa,nis',
                'nama' => 'required|string|max:100',
                'kelas' => 'required|string|max:50',
                'jurusan' => 'required|string|max:100',
            ]);

            $siswa = Siswa::create($validated);

            return ApiResponse::response(
                $siswa,
                'Data siswa berhasil ditambahkan',
                201
            );

        } catch (ValidationException $e) {
            return ApiResponse::response(
                $e->errors(),
                'Validasi gagal',
                422
            );
        } catch (Throwable $e) {
            Log::error('Gagal menambahkan siswa: ' . $e->getMessage());

            return ApiResponse::response(
                null,
                'Gagal menambahkan data siswa',
                500
            );
        }
    }

    // MENAMPILKAN SATU SISWA
    public function show(string $id)
    {
        try {
            $siswa = Siswa::find($id);

            if (!$siswa) {
                return ApiResponse::response(
                    null,
                    'Data siswa tidak ditemukan',
                    404
                );
            }

            return ApiResponse::response(
                $siswa,
                'Data siswa berhasil ditemukan',
                200
            );

        } catch (Throwable $e) {
            Log::error('Gagal mencari siswa: ' . $e->getMessage());

            return ApiResponse::response(
                null,
                'Gagal mencari data siswa',
                500
            );
        }
    }

    // MENGUBAH DATA SISWA
    public function update(Request $request, string $id)
    {
        try {
            $siswa = Siswa::find($id);

            if (!$siswa) {
                return ApiResponse::response(
                    null,
                    'Data siswa tidak ditemukan',
                    404
                );
            }

            $validated = $request->validate([
                'nis' => 'sometimes|required|string|max:30|unique:siswa,nis,' . $siswa->id,
                'nama' => 'sometimes|required|string|max:100',
                'kelas' => 'sometimes|required|string|max:50',
                'jurusan' => 'sometimes|required|string|max:100',
            ]);

            $siswa->update($validated);

            return ApiResponse::response(
                $siswa->fresh(),
                'Data siswa berhasil diperbarui',
                200
            );

        } catch (ValidationException $e) {
            return ApiResponse::response(
                $e->errors(),
                'Validasi gagal',
                422
            );
        } catch (Throwable $e) {
            Log::error('Gagal memperbarui siswa: ' . $e->getMessage());

            return ApiResponse::response(
                null,
                'Gagal memperbarui data siswa',
                500
            );
        }
    }

    // MENGHAPUS SISWA
    public function destroy(string $id)
    {
        try {
            $siswa = Siswa::find($id);

            if (!$siswa) {
                return ApiResponse::response(
                    null,
                    'Data siswa tidak ditemukan',
                    404
                );
            }

            $siswa->delete();

            return ApiResponse::response(
                null,
                'Data siswa berhasil dihapus',
                200
            );

        } catch (Throwable $e) {
            Log::error('Gagal menghapus siswa: ' . $e->getMessage());

            return ApiResponse::response(
                null,
                'Gagal menghapus data siswa',
                500
            );
        }
    }
}