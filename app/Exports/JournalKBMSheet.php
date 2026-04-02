<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class JournalKBMSheet implements FromArray, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['JURNAL KBM'];
        $rows[] = ['Kelas', $this->data['grade']];
        $rows[] = ['Tanggal', Carbon::parse($this->data['date'])->locale('id')->translatedFormat('d F Y')];
        $rows[] = [];

        $rows[] = ['Mapel', 'Guru', 'Waktu', 'Keterangan'];

        foreach ($this->data['notes'] as $n) {
            $rows[] = [
                $n['subject'],
                $n['teacher'],
                $n['time'],
                $n['note'],
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'KBM';
    }
}
