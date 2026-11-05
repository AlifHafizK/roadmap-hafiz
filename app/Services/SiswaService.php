<?php

namespace App\Services;

use App\Repositories\SiswaRepository;
use Illuminate\Support\Facades\Cache;

class SiswaService
{
    protected $repository;

    public function __construct(SiswaRepository $repository)
    {
        $this->repository = $repository;
    }

    // Mengambil semua data siswa dengan Redis Cache
    public function index()
    {
        $cache = Cache::store('redis');

        // Periksa apakah data tersedia di Redis
        $fromCache = $cache->has('siswa');

        // Ambil data dari Redis atau Database
        $siswa = $cache->remember(
            'siswa',
            60,
            function () {
                return $this->repository->index();
            }
        );

        return [
            'source' => $fromCache ? 'cache' : 'database',
            'data' => $siswa
        ];
    }

    // Mengambil satu data siswa
    public function show($id)
    {
        return $this->repository->show($id);
    }

    // Menambahkan siswa dan menghapus cache lama
    public function store(array $data)
    {
        $result = $this->repository->store($data);

        Cache::store('redis')->forget('siswa');

        return $result;
    }

    // Memperbarui siswa dan menghapus cache lama
    public function update($id, array $data)
    {
        $result = $this->repository->update($id, $data);

        Cache::store('redis')->forget('siswa');

        return $result;
    }

    // Menghapus siswa dan menghapus cache lama
    public function destroy($id)
    {
        $result = $this->repository->destroy($id);

        Cache::store('redis')->forget('siswa');

        return $result;
    }
}
