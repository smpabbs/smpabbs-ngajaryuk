<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Presensi {{ $grade }} - Semester {{ $semester }} - Tahun {{ $year }}</title>
    <style>
        @page {
            margin: 0.8cm;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8.5px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .header h2 {
            margin: 2px 0;
            font-size: 11px;
            color: #555;
        }
        .header h3 {
            margin: 2px 0;
            font-size: 10px;
        }
        .header p {
            margin: 2px 0;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 2px 1px;
            text-align: center;
            height: 12px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 7.5px;
        }
        .name-col {
            width: 1%;
            text-align: left;
            padding-left: 4px;
            white-space: nowrap;
        }
        .no-col {
            width: 1%;
        }
        .day-col {
            
        }
        .stat-col {
            width: 1%;
            font-weight: bold;
        }
        .status-s { background-color: #d1f7ff; color: #007691; }
        .status-i { background-color: #fff4d1; color: #856404; }
        .status-a { background-color: #ffd1d1; color: #721c24; }
        
        .page-break {
            page-break-after: always;
        }
        .summary-header {
            background-color: #e3f2fd;
            font-size: 12px;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #90caf9;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>JOURNAL OF SUBJECT</h1>
        <h2>ABBS JUNIOR HIGH SCHOOL</h2>
        <h3>REKAP PRESENSI - CLASS {{ $grade }}</h3>
        @php
            $academicYear = ($semester == 2) ? $year . '/' . ($year + 1) : ($year - 1) . '/' . $year;
        @endphp
        <p>Semester: {{ $semester }} | Academic Year: {{ $academicYear }}</p>
    </div>

    @php
        $monthsArr = [];
        for ($m = $startMonth; $m <= $endMonth; $m++) {
            $monthsArr[] = $m;
        }
    @endphp

    @foreach ($monthsArr as $monthIdx)
        @php
            $carbonMonth = \Carbon\Carbon::create($year, $monthIdx, 1);
            $daysInMonth = $carbonMonth->daysInMonth;
        @endphp

        <div style="margin-bottom: 10px; font-weight: bold; font-size: 12px;">
            MONTH: {{ strtoupper($carbonMonth->translatedFormat('F Y')) }}
        </div>

        <table>
            <thead>
                <tr>
                    <th class="no-col">NO</th>
                    <th class="name-col">STUDENT NAME</th>
                    @for ($d = 1; $d <= 31; $d++)
                        <th class="day-col" @if($d > $daysInMonth) style="background:#eee;" @endif>{{ $d }}</th>
                    @endfor
                    <th class="stat-col" style="color: #28a745;">S</th>
                    <th class="stat-col" style="color: #ffc107;">I</th>
                    <th class="stat-col" style="color: #dc3545;">A</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="name-col">{{ $student->name }}</td>
                        @for ($d = 1; $d <= 31; $d++)
                            @php
                                $val = $attendanceMap[$student->id][$monthIdx][$d] ?? '';
                                $class = '';
                                if ($val == 'S') $class = 'status-s';
                                elseif ($val == 'I') $class = 'status-i';
                                elseif ($val == 'A') $class = 'status-a';
                            @endphp
                            <td class="day-col {{ $class }}" @if($d > $daysInMonth) style="background:#f9f9f9;" @endif>
                                {{ $val }}
                            </td>
                        @endfor
                        
                        @php
                            $mS = 0; $mI = 0; $mA = 0;
                            if (isset($attendanceMap[$student->id][$monthIdx])) {
                                foreach ($attendanceMap[$student->id][$monthIdx] as $v) {
                                    if ($v == 'S') $mS++;
                                    elseif ($v == 'I') $mI++;
                                    elseif ($v == 'A') $mA++;
                                }
                            }
                        @endphp
                        <td class="stat-col">{{ $mS ?: '' }}</td>
                        <td class="stat-col">{{ $mI ?: '' }}</td>
                        <td class="stat-col">{{ $mA ?: '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        @if (!$loop->last && $viewType == 'semester')
            <div class="page-break"></div>
        @endif
    @endforeach

    @if ($viewType == 'semester')
        <div class="page-break"></div>
        <div class="summary-header">
            SEMESTER {{ $semester }} ATTENDANCE SUMMARY (ACADEMIC YEAR {{ $academicYear }})
        </div>
        <table style="margin-top: 10px;">
            <thead>
                <tr>
                    <th style="width: 40px;">NO</th>
                    <th style="text-align: left; padding-left: 10px; width: 60%; auto;">STUDENT NAME</th>
                    <th style="width: 80px; color: #28a745;">TOTAL S</th>
                    <th style="width: 80px; color: #ffc107;">TOTAL I</th>
                    <th style="width: 80px; color: #dc3545;">TOTAL A</th>
                    <th style="width: 100px; background: #e3f2fd; color: #0056b3;">TOTAL ABSENT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $index => $student)
                    @php
                        $sS = $summary[$student->id]['S'] ?? 0;
                        $sI = $summary[$student->id]['I'] ?? 0;
                        $sA = $summary[$student->id]['A'] ?? 0;
                        $total = $sS + $sI + $sA;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: left; padding-left: 10px; font-weight: bold;">{{ $student->name }}</td>
                        <td style="font-size: 11px; color: #28a745; font-weight: bold;">{{ $sS }} Days</td>
                        <td style="font-size: 11px; color: #d39e00; font-weight: bold;">{{ $sI }} Days</td>
                        <td style="font-size: 11px; color: #dc3545; font-weight: bold;">{{ $sA }} Days</td>
                        <td style="font-size: 12px; font-weight: bold; background: #f0f7ff; color: #0056b3;">{{ $total }} Days</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>
