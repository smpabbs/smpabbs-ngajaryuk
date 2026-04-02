<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class TeacherController extends Controller
{
    /**
     * Store a newly created teacher.
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validasi Request
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:teachers',
            'password' => 'required|string|min:6',
            'kelas'    => 'nullable|array',
            'mapel'    => 'nullable|array',
        ]);

        try {
            // 2. Format data mapel: pasangkan kelas[] dengan mapel[]
            $kelas = $request->input('kelas', []);
            $mapel = $request->input('mapel', []);
            
            $mapelData = [];
            foreach ($kelas as $index => $k) {
                if (!empty($k) && !empty($mapel[$index])) {
                    $mapelData[] = [
                        'kelas' => $k,
                        'mapel' => $mapel[$index]
                    ];
                }
            }

            // 3. Simpan data guru menggunakan Hash facade (standar Laravel)
            $teacher = Teacher::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password), // Gunakan Hash::make() vs bcrypt()
                'mapel'    => $mapelData
            ]);

            return response()->json([
                'status'  => 'success',
                'teacher' => $teacher
            ], 201); // 201 Created

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menambahkan guru: ' . $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }
}
