<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin
        User::updateOrCreate(
            ['email' => 'admin@abbs.test'],
            [
                'name' => 'AdminABBS',
                'email' => 'admin@abbs.test',
                'password' => Hash::make('smpABBS_2025admin'),
                'is_admin' => 1,
                'mapel' => json_encode(['admin']),
            ]
        );
        $teachers = [
            // Tambahkan data guru di sini dengan format yang sesuai
        ];

        foreach ($teachers as $teacher) {
        $mapel = [];

        if (isset($teacher['mapel']) && is_array($teacher['mapel'])) {
            foreach ($teacher['mapel'] as $key => $value) {
                if (is_int($key)) {
                    // Indexed array → jadikan key sama value
                    $mapel[$value] = $value;

                } elseif ($value === 'Leadership' && ctype_digit((string)$key)) {
                    // kalau key numeric / kelas
                    $mapel[$key] = $value;

                } elseif ($value === 'Leadership' && !ctype_digit((string)$key)) {
                    // kalau key bukan angka, tapi Leadership → map otomatis ke kelas default
                    // misal kelas 7–9
                    for ($kelas = 7; $kelas <= 9; $kelas++) {
                        $mapel[$kelas] = 'Leadership';
                    }

                } else {
                    $mapel[$key] = $value;
                }
            }
        }

    User::updateOrCreate(
        ['email' => $teacher['email']],
        [
            'name' => $teacher['name'] ?? 'Unknown',
            'password' => $teacher['pw'] ?? bcrypt('default123'),
            'is_admin' => 0,
            'mapel' => json_encode($mapel),
        ]
    );
}

    }
}
