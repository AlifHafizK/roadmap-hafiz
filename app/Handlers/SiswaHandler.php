<?php

namespace App\Handlers;

use App\Repositories\SiswaRepository;

class SiswaHandler
{
    protected $repository;

    public function __construct(SiswaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->index();
    }

    public function show($id)
    {
        $siswa = $this->repository->show($id);

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan');
        }

        return $siswa;
    }

    public function store(array $data)
    {
        return $this->repository->store($data);
    }

    public function update($id, array $data)
    {
        $siswa = $this->repository->show($id);

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan');
        }

        return $this->repository->update($id, $data);
    }

    public function destroy($id)
    {
        $siswa = $this->repository->show($id);

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan');
        }

        return $this->repository->destroy($id);
    }
}
