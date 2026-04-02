<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class StudentsMultiSheetImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => new SingleSheetImport(),
            1 => new SingleSheetImport(),
            2 => new SingleSheetImport(),
        ];
    }
}

class SingleSheetImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Pra-muat (Pre-fetch) data cache untuk mencegah N+1 Select Query
        $existingStudents = Student::select('name', 'grade')
            ->get()
            ->map(fn($s) => strtolower($s->name . '|' . $s->grade))
            ->flip();

        $inserts = [];
        $now = now();

        foreach ($rows as $row) {
            // Asumsi baris indeks ke-1 adalah Nama dan ke-2 adalah Kelas
            $name  = trim($row[1] ?? '');
            $grade = trim($row[2] ?? '');

            // Skip baris kosong atau header string mentah
            if ($name === '' || $grade === '' || strtolower($name) === 'nama' || strtolower($grade) === 'kelas') {
                continue;
            }

            $key = strtolower($name . '|' . $grade);

            // Cek index memori lokal
            if (!$existingStudents->has($key)) {
                $inserts[$key] = [
                    'name'  => $name,
                    'grade' => $grade,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                
                // Daftarkan ke cache memori lokal agar duplikat berurutan dalam file Excel terlewatkan
                $existingStudents->put($key, true);
            }
        }

        // Jalankan Batch Insert Database 1x
        if (!empty($inserts)) {
            Student::insert(array_values($inserts));
        }
    }
}
