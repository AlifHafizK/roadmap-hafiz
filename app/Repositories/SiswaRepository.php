<?php

namespace App\Repositories;

use App\Interfaces\SiswaInterface;
use App\Models\Siswa;

class SiswaRepository implements SiswaInterface
{
    public function index()
    {
        return Siswa::all();
    }

    public function store(array $data)
    {
        return Siswa::create($data);
    }

    public function show($id)
    {
        return Siswa::find($id);
    }

    public function update($id, array $data)
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return null;
        }

        $siswa->update($data);

        return $siswa;
    }

    public function destroy($id)
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return false;
        }

        return $siswa->delete();
    }
}
