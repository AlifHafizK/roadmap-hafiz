<?php

namespace App\Handlers;

use App\Services\SiswaService;

class SiswaHandler
{
    protected $service;

    public function __construct(SiswaService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->index();
    }

    public function show($id)
    {
        $siswa = $this->service->show($id);

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan');
        }

        return $siswa;
    }

    public function store(array $data)
    {
        return $this->service->store($data);
    }

    public function update($id, array $data)
    {
        $siswa = $this->service->show($id);

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan');
        }

        return $this->service->update($id, $data);
    }

    public function destroy($id)
    {
        $siswa = $this->service->show($id);

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan');
        }

        return $this->service->destroy($id);
    }
}