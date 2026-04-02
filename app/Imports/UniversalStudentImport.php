<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class UniversalStudentImport implements ToModel, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1;
    }

    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $k => $v) {

            // Buang karakter aneh
            $newKey = Str::of($k)
                ->lower()
                ->replace([' ', '_', '.', '-', ':'], '') // buang titik, dash, dll
                ->trim();

            $normalized[$newKey] = $v;
        }
        return $normalized;
    }


    public function model(array $row)
    {
        $row = $this->normalizeRow($row);

        $name  = trim($row['nama'] ?? $row['name'] ?? '');
        $grade = trim($row['kelas'] ?? $row['grade'] ?? '');

        if ($name === '' || $grade === '') {
            return null;
        }

        $exists = Student::where('name', $name)
            ->where('grade', $grade)
            ->exists();

        if ($exists) {
            return null;
        }

        return new Student([
            'name'  => $name,
            'grade' => $grade,
        ]);
    }

}
