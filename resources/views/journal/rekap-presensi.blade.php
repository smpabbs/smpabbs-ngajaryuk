@extends('layouts.app')

@section('title', 'Rekap Presensi - ' . $grade)

@section('content')
    <div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: #f8fafc;">

        <div class="position-relative">
            <button type="button" class="btn btn-outline-info btn-sm px-4 py-2 fw-bold"
                onclick="window.location.href = '{{ route('rekap.index') }}'" style="border-width: 2px; min-width: 200px;">
                <i class="fas fa-chevron-left me-2"></i>Kembali
            </button>
        </div>

        <!-- Branding Header -->
        <div class="text-center mb-5 mt-2">
            <h1 class="h2 fw-900 text-white mb-1" style="letter-spacing: 2px;">JOURNAL OF SUBJECT</h1>
            <h2 class="h5 fw-700 text-info opacity-75 mb-3">ABBS JUNIOR HIGH SCHOOL</h2>
        </div>

        <!-- Header Section -->
        <div class="d-flex align-items-center justify-content-between mb-5 flex-wrap gap-4 no-print mt-4">
            <div class="d-flex align-items-center">
                <h1 class="h2 fw-800 text-white mb-0 me-3">{{ $grade }}</h1>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="{{ route('rekap.index') }}"
                                class="text-info text-decoration-none fw-bold" style="color: #60a5fa !important;">Rekap</a>
                        </li>
                        <li class="breadcrumb-item active text-light opacity-75">Presensi Siswa</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-3">
                <div class="btn-group btn-group-sm no-print" role="group">
                    <a href="{{ route('rekap.showPresensi', ['class' => $grade, 'view' => 'semester', 'semester' => $semester, 'year' => $year]) }}"
                        class="btn {{ $viewType == 'semester' ? 'btn-info' : 'btn-outline-info' }} fw-bold">Semua</a>
                    <a href="{{ route('rekap.showPresensi', ['class' => $grade, 'view' => 'monthly', 'semester' => $semester, 'year' => $year, 'month' => $selectedMonth]) }}"
                        class="btn {{ $viewType == 'monthly' ? 'btn-info' : 'btn-outline-info' }} fw-bold">Per Bulan</a>
                </div>

                <form action="{{ route('rekap.showPresensi') }}" method="GET" id="semesterForm"
                    class="d-flex align-items-center gap-2 mb-0">
                    <input type="hidden" name="class" value="{{ $grade }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="view" value="{{ $viewType }}">
                    

                    @if($viewType == 'semester')
                        <span class="text-light small fw-bold">Semester:</span>
                        <select name="semester" class="form-select form-select-sm custom-dark-select" onchange="this.form.submit()"
                            style="min-width: 120px; background-color: #111827; color: #fff; border-color: rgba(255,255,255,0.2);">
                            <option value="1" {{ $semester == 1 ? 'selected' : '' }}>1 (Jan - Jun)</option>
                            <option value="2" {{ $semester == 2 ? 'selected' : '' }}>2 (Jul - Des)</option>
                        </select>
                    @else
                        <input type="hidden" name="semester" value="{{ $semester }}">
                        <span class="text-light small fw-bold">Bulan:</span>
                        <select name="month" class="form-select form-select-sm custom-dark-select" onchange="this.form.submit()"
                            style="min-width: 150px; background-color: #111827; color: #fff; border-color: rgba(255,255,255,0.2);">
                            @php
                                $mStart = ($semester == 1) ? 1 : 7;
                                $mEnd = ($semester == 1) ? 6 : 12;
                            @endphp
                            @for($m = $mStart; $m <= $mEnd; $m++)
                                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2000, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    @endif
                </form>

                @if(Auth::user()->is_admin)
                    <a href="{{ route('rekap.pdf', request()->all()) }}" target="_blank" class="btn btn-primary px-4 py-2 fw-bold shadow-sm no-print">
                        <i class="fas fa-print me-2"></i>Cetak PDF
                    </a>
                @endif
            </div>
        </div>

        @php
            $months = [];
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $months[] = $m;
            }
        @endphp

        @foreach ($months as $month)
            @php
                $carbonMonth = \Carbon\Carbon::create($year, $month, 1);
                $daysInMonth = $carbonMonth->daysInMonth;
            @endphp
            
            <div class="rekap-table-container shadow-lg border-radius-xl overflow-hidden mb-5"
                style="background: #1e293b; border: 1px solid rgba(148, 163, 184, 0.15);">
                <div class="p-4 border-bottom"
                    style="background: rgba(255, 255, 255, 0.01); border-color: rgba(148, 163, 184, 0.15) !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-800 text-white opacity-90">
                            <i class="fas fa-calendar-alt me-2 text-info"></i>{{ $carbonMonth->translatedFormat('F Y') }}
                        </h5>
                    </div>
                </div>

                <div class="table-responsive p-0">
                    <table class="table table-bordered align-middle mb-0" style="table-layout: fixed; width: 100%;">
                        <thead>
                            <tr style="background-color: rgba(255, 255, 255, 0.03) !important;">
                                <th class="ps-3 text-center text-info" style="width: 40px; font-size: 0.7rem; font-weight: 800; border-color: rgba(148, 163, 184, 0.15);">NO</th>
                                <th class="ps-2 text-info" style="width: 180px; font-size: 0.7rem; font-weight: 800; border-color: rgba(148, 163, 184, 0.15);">NAMA SISWA</th>
                                @for ($d = 1; $d <= 31; $d++)
                                    <th class="text-center text-info p-1" style="width: 30px; font-size: 0.65rem; font-weight: 800; border-color: rgba(148, 163, 184, 0.15); {{ $d > $daysInMonth ? 'opacity: 0.1; background: #0f172a;' : '' }}">
                                        {{ $d }}
                                    </th>
                                @endfor
                                <th class="text-center text-success" style="width: 30px; font-size: 0.7rem; font-weight: 900; background: rgba(16, 185, 129, 0.05); border-color: rgba(148, 163, 184, 0.15);">S</th>
                                <th class="text-center text-warning" style="width: 30px; font-size: 0.7rem; font-weight: 900; background: rgba(245, 158, 11, 0.05); border-color: rgba(148, 163, 184, 0.15);">I</th>
                                <th class="text-center text-danger" style="width: 30px; font-size: 0.7rem; font-weight: 900; background: rgba(239, 68, 68, 0.05); border-color: rgba(148, 163, 184, 0.15);">A</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $index => $student)
                                <tr style="border-bottom: 1px solid rgba(148, 163, 184, 0.1);">
                                    <td class="text-center text-muted small" style="border-color: rgba(148, 163, 184, 0.15);">{{ $index + 1 }}</td>
                                    <td class="ps-2 fw-bold text-white opacity-90" style="font-size: 0.85rem; border-color: rgba(148, 163, 184, 0.15); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $student->name }}
                                    </td>
                                    @for ($d = 1; $d <= 31; $d++)
                                        @php
                                            $val = $attendanceMap[$student->id][$month][$d] ?? '';
                                            $statusClass = '';
                                            if ($val == 'S') $statusClass = 'status-s';
                                            elseif ($val == 'I') $statusClass = 'status-i';
                                            elseif ($val == 'A') $statusClass = 'status-a';
                                        @endphp
                                        <td class="text-center p-0 {{ $statusClass }}" 
                                            style="height: 35px; {{ $d > $daysInMonth ? 'background: #0f172a; opacity: 0.1;' : '' }} font-weight: 800; font-size: 0.75rem;">
                                            {{ $val }}
                                        </td>
                                    @endfor
                                    
                                    {{-- Summary columns for the CURRENT month only would be confusing if we already have a semester rekap --}}
                                    {{-- But the user wants it like class-detail, so I will show the MONTHLY summary here --}}
                                    @php
                                        // Calculate monthly summary for this student
                                        $mS = 0; $mI = 0; $mA = 0;
                                        if (isset($attendanceMap[$student->id][$month])) {
                                            foreach ($attendanceMap[$student->id][$month] as $v) {
                                                if ($v == 'S') $mS++;
                                                elseif ($v == 'I') $mI++;
                                                elseif ($v == 'A') $mA++;
                                            }
                                        }
                                    @endphp
                                    <td class="text-center fw-bold" style="background: rgba(16, 185, 129, 0.05); color: #34d399; font-size: 0.75rem; border-color: rgba(148, 163, 184, 0.15);">{{ $mS ?: '' }}</td>
                                    <td class="text-center fw-bold" style="background: rgba(245, 158, 11, 0.05); color: #fbbf24; font-size: 0.75rem; border-color: rgba(148, 163, 184, 0.15);">{{ $mI ?: '' }}</td>
                                    <td class="text-center fw-bold" style="background: rgba(239, 68, 68, 0.05); color: #f87171; font-size: 0.75rem; border-color: rgba(148, 163, 184, 0.15);">{{ $mA ?: '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <!-- Semester Summary Section -->
        <div class="rekap-table-container shadow-lg border-radius-xl overflow-hidden mb-5"
            style="background: #1e293b; border: 2px solid var(--primary-color);">
            <div class="p-4 border-bottom"
                style="background: rgba(96, 165, 250, 0.1); border-color: var(--primary-color) !important;">
                <h5 class="mb-0 fw-800 text-white">
                    <i class="fas fa-chart-pie me-2 text-info"></i>Ringkasan Absensi Semester {{ $semester }}
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr style="background: rgba(255,255,255,0.02);">
                            <th class="ps-4 py-3 text-info" style="font-weight: 800; font-size: 0.8rem;">NAMA SISWA</th>
                            <th class="text-center text-success" style="font-weight: 800; font-size: 0.8rem;">TOTAL SAKIT (S)</th>
                            <th class="text-center text-warning" style="font-weight: 800; font-size: 0.8rem;">TOTAL IJIN (I)</th>
                            <th class="text-center text-danger" style="font-weight: 800; font-size: 0.8rem;">TOTAL ALPHA (A)</th>
                            <th class="text-center text-info" style="font-weight: 800; font-size: 0.8rem;">TOTAL TIDAK HADIR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            @php
                                $sS = $summary[$student->id]['S'] ?? 0;
                                $sI = $summary[$student->id]['I'] ?? 0;
                                $sA = $summary[$student->id]['A'] ?? 0;
                                $total = $sS + $sI + $sA;
                            @endphp
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td class="ps-4 fw-bold text-white">{{ $student->name }}</td>
                                <td class="text-center fs-5 fw-800" style="color: #34d399;">{{ $sS }}</td>
                                <td class="text-center fs-5 fw-800" style="color: #fbbf24;">{{ $sI }}</td>
                                <td class="text-center fs-5 fw-800" style="color: #f87171;">{{ $sA }}</td>
                                <td class="text-center fs-5 fw-900" style="color: #60a5fa; background: rgba(96, 165, 250, 0.05);">{{ $total }} Hari</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        :root {
            --primary-color: #60a5fa;
            --bg-page: #0f172a;
            --bg-card: #1e293b;
            --border-color: rgba(148, 163, 184, 0.12);
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-page) !important;
            color: var(--text-main);
        }

        .rekap-table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .table {
            color: var(--text-main) !important;
            background-color: transparent !important;
        }

        .table td, .table th {
            background-color: transparent;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }

        .status-s { background-color: rgba(13, 202, 240, 0.25) !important; color: #67e8f9 !important; }
        .status-i { background-color: rgba(255, 193, 7, 0.25) !important; color: #fde047 !important; }
        .status-a { background-color: rgba(220, 53, 69, 0.25) !important; color: #fca5a5 !important; }

        .table thead th {
            background-color: rgba(255, 255, 255, 0.02) !important;
            color: #7dd3fc !important;
            border-bottom: 2px solid var(--border-color) !important;
        }

        .table-bordered > :not(caption) > * > * {
            border-width: 1px !important;
        }

        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }

        .custom-dark-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        }

        .fw-800 { font-weight: 800; }
        .fw-900 { font-weight: 900; }

        @media print {
            .no-print { display: none !important; }
            body { background-color: #fff !important; color: #000 !important; }
            .rekap-table-container { 
                box-shadow: none !important; 
                border: 1px solid #000 !important;
                background: #fff !important;
            }
            .text-white, .text-info, .text-success, .text-warning, .text-danger, .table td, .table th { color: #000 !important; }
            th { border: 1px solid #000 !important; }
            td { border: 1px solid #000 !important; }
        }
    </style>
@endsection
