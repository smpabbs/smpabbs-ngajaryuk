<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Student;

class UniversalMultiSheetImport implements ToModel
{
    private function normalizeRow(array $row): array
{
    $normalized = [];
    foreach ($row as $k => $v) {
        $newKey = \Illuminate\Support\Str::of($k)
            ->lower()
            ->replace([' ', '_', '.', '-', ':'], '') // buang karakter aneh
            ->trim();
        $normalized[$newKey] = $v;
    }
    return $normalized;
}


    public function model(array $row)
{
    $row = $this->normalizeRow($row); // normalize dulu

    return new Student([
        'name'  => $row['nama'] ?? $row['name'] ?? 'Unnamed',
        'grade' => $row['kelas'] ?? $row['grade'] ?? 'Unknown',
    ]);
}

}
