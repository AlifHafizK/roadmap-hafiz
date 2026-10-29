<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SiswaImport implements
    ToModel,
    WithHeadingRow,
    WithBatchInserts,
    WithChunkReading
{
    public function model(array $row)
    {
        $siswa = Siswa::where('name', $row['name'])
            ->where('kelas', $row['kelas'])
            ->where('umur', $row['umur'])
            ->first();

        if ($siswa) {
            return null;
        }

        return new Siswa([
            'name'  => $row['name'],
            'kelas' => $row['kelas'],
            'umur'  => $row['umur'],
        ]);
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
