<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class JournalAttendanceSheet implements FromArray, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];

        // HEADER
        $header = ['No', 'Nama'];
        for ($i = 1; $i <= 31; $i++) {
            $header[] = $i;
        }
        $header[] = 'S';
        $header[] = 'I';
        $header[] = 'A';

        $rows[] = ['ABSENSI SISWA'];
        $rows[] = ['Kelas', $this->data['grade']];
        $rows[] = ['Bulan', $this->data['month'], 'Tahun', $this->data['year']];
        $rows[] = [];
        $rows[] = $header;

        // DATA
        foreach ($this->data['students'] as $i => $s) {
            $row = [
                $i + 1,
                $s['name'],
            ];

            for ($d = 1; $d <= 31; $d++) {
                $row[] = $s['attendance'][$d] ?? '';
            }

            $row[] = $s['summary']['S'] ?? 0;
            $row[] = $s['summary']['I'] ?? 0;
            $row[] = $s['summary']['A'] ?? 0;

            $rows[] = $row;
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Absensi';
    }
}
