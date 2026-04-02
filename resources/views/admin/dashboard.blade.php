@extends('layouts.app')

@section('title', 'Admin Panel')

@section('content')


    <style>
        .dashboard-header {
            background: var(--bg-card);
            color: var(--text-main);
            padding: 2.5rem;
            border-radius: var(--card-radius);
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        #usersTableWrapper {
            max-height: 450px;
            overflow-y: auto;
        }

        .dashboard-header::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(59, 130, 246, 0.05) 100%);
            pointer-events: none;
        }

        .dashboard-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .time-info #clock {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-color);
            line-height: 1;
        }

        .stats-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--bg-card);
            border: 1px solid var(--border-color);
        }

        .stats-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.4);
        }

        .stats-card .card-body {
            padding: 2.5rem 1.5rem;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 1.25rem;
            transition: all 0.3s ease;
        }

        .stats-card.bg-primary-light {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, var(--bg-card) 100%) !important;
        }

        .stats-card.bg-primary-light .stats-icon {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }

        .stats-card.bg-success-light {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, var(--bg-card) 100%) !important;
        }

        .stats-card.bg-success-light .stats-icon {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }

        .stats-card.bg-info-light {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.08) 0%, var(--bg-card) 100%) !important;
        }

        .stats-card.bg-info-light .stats-icon {
            background: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
        }

        .stats-card.bg-warning-light {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, var(--bg-card) 100%) !important;
        }

        .stats-card.bg-warning-light .stats-icon {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }

        .stats-card h5 {
            font-size: 0.8rem;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .stats-card h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
        }

        .users-section {
            background: var(--bg-card);
            padding: 2.5rem;
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
        }

        .section-title-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-color);
        }

        .section-title-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border-radius: 12px;
            font-size: 1.25rem;
            margin-right: 1rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .table thead th {
            background-color: rgba(255, 255, 255, 0.03);
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-color);
        }

        .table-container {
            background: var(--bg-card);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .table {
            background: var(--bg-card);
            margin-bottom: 0;
        }

        .table tbody tr {
            background: var(--bg-card);
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .no-data {
            text-align: center;
            padding: 3rem 1rem !important;
            color: var(--text-muted);
        }

        .no-data i {
            font-size: 3rem;
            opacity: 0.3;
            display: block;
            margin-bottom: 1rem;
        }

        .search-box {
            background: #0f172a;
            border: 1.5px solid var(--border-color);
            color: var(--text-main);
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }

        .search-box:focus {
            background: #0f172a;
            border-color: var(--primary-color);
            color: var(--text-main);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        .action-btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 8px;
        }
    </style>

    <div class="container pb-5">
        <!-- Header -->
        <div class="dashboard-header animate__animated animate__fadeIn">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h2 class="mb-1" id="greeting">Selamat datang!</h2>
                    <p class="mb-0">Kelola data absensi, jurnal, dan jadwal dalam satu panel kontrol pusat.
                    </p>
                </div>
                <div class="col-md-5 text-md-end mt-4 mt-md-0">
                    <div class="time-info">
                        <div id="clock">--:--:--</div>
                        <div id="date" class="fw-700">--</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card stats-card bg-primary-light clickable" onclick="showTeachersPopup()"
                    style="cursor: pointer; user-select: none; background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, var(--bg-card) 100%) !important;">
                    <div class="card-body">
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5>Total Guru</h5>
                        <h2>{{ $totalTeachers }}</h2>
                        <p
                            style="font-size: 0.85rem; color: var(--accent-blue); margin-top: 10px; cursor: pointer; margin-bottom: 0;">
                            <i class="fas fa-touch-pointer"></i> Ketuk untuk detail
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stats-card bg-success-light"
                    style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, var(--bg-card) 100%) !important;">
                    <div class="card-body">
                        @php $today = new DateTime(); @endphp
                        <div class="stats-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h5>Total Absensi</h5>
                        <h2 onclick="showAbsenciPopup()" style="cursor: pointer;">{{ $absensiCount }}</h2>
                        <p style="font-size: 0.85rem; color: var(--accent-green); margin-top: 10px; cursor: pointer; margin-bottom: 0;"
                            onclick="showAbsenciPopup()">
                            <i class="fas fa-touch-pointer"></i> Ketuk untuk detail
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes and Students Counter -->
        <div class="row mb-4">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                <div class="card stats-card bg-info-light clickable" onclick="showClassesPopup()"
                    style="cursor: pointer; user-select: none; background: linear-gradient(135deg, rgba(14, 165, 233, 0.08) 0%, var(--bg-card) 100%) !important;">
                    <div class="card-body">
                        <div class="stats-icon">
                            <i class="fas fa-school"></i>
                        </div>
                        <h5>Total Kelas</h5>
                        <h2>{{ $totalClasses }}</h2>
                        <p
                            style="font-size: 0.85rem; color: var(--accent-info); margin-top: 10px; cursor: pointer; margin-bottom: 0;">
                            <i class="fas fa-touch-pointer"></i> Ketuk untuk detail
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                <div class="card stats-card bg-warning-light clickable" onclick="showStudentsPopup()"
                    style="cursor: pointer; user-select: none; background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, var(--bg-card) 100%) !important;">
                    <div class="card-body">
                        <div class="stats-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5>Total Siswa</h5>
                        <h2>{{ $totalStudents }}</h2>
                        <p
                            style="font-size: 0.85rem; color: var(--accent-warning); margin-top: 10px; cursor: pointer; margin-bottom: 0;">
                            <i class="fas fa-touch-pointer"></i> Ketuk untuk detail
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        <div class="users-section">
            <div class="section-title-wrapper">
                <div class="section-title-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <h2 class="section-title">Teachers Management</h2>
            </div>
            <div class="mb-3">
                <input type="text" id="searchUsers" class="form-control search-box"
                    placeholder="Search by name or email..."
                    style="background-color: #0f172a; color: var(--text-main); border: 1px solid var(--border-color);">

                <style>
                    #searchUsers::placeholder {
                        color: white;
                        opacity: 1;
                    }
                </style>
            </div>

            <div id="usersTableWrapper"
                style="background: var(--bg-card); border-radius: 10px; overflow-y: auto; max-height: 450px; border: 1px solid var(--border-color);">
                <table class="table table-hover" id="usersTable" style="background: var(--bg-card); margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th
                                style="background-color: rgba(255, 255, 255, 0.03); font-weight: 800; color: #cbd5e1; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; padding: 1rem; border-bottom: 2px solid var(--border-color);">
                                <i class="fas fa-hashtag table-icon"></i>No
                            </th>
                            <th
                                style="background-color: rgba(255, 255, 255, 0.03); font-weight: 800; color: #cbd5e1; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; padding: 1rem; border-bottom: 2px solid var(--border-color);">
                                <i class="fas fa-user table-icon"></i>Name
                            </th>
                            <th
                                style="background-color: rgba(255, 255, 255, 0.03); font-weight: 800; color: #cbd5e1; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; padding: 1rem; border-bottom: 2px solid var(--border-color);">
                                <i class="fas fa-envelope table-icon"></i>Email
                            </th>
                            <th
                                style="background-color: rgba(255, 255, 255, 0.03); font-weight: 800; color: #cbd5e1; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; padding: 1rem; border-bottom: 2px solid var(--border-color);">
                                <i class="fas fa-sliders-h table-icon"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $teachers = $users->where('is_admin', 0); @endphp
                        @if ($teachers->count() > 0)
                            @foreach ($teachers as $i => $user)
                                <tr
                                    style="background-color: var(--bg-card) !important; color: var(--text-main) !important;">
                                    <td
                                        style="background-color: var(--bg-card) !important; padding: 1rem; vertical-align: middle; color: var(--text-main) !important; border-bottom: 1px solid var(--border-color);">
                                        <strong>{{ $loop->iteration }}</strong>
                                    </td>
                                    <td
                                        style="background-color: var(--bg-card) !important; padding: 1rem; vertical-align: middle; color: var(--text-main) !important; border-bottom: 1px solid var(--border-color);">
                                        <strong>{{ $user->name }}</strong>
                                    </td>
                                    <td
                                        style="background-color: var(--bg-card) !important; padding: 1rem; vertical-align: middle; color: var(--text-main) !important; border-bottom: 1px solid var(--border-color);">
                                        <span>{{ $user->email }}</span>
                                    </td>
                                    <td
                                        style="background-color: var(--bg-card) !important; padding: 1rem; vertical-align: middle; color: var(--text-main) !important; border-bottom: 1px solid var(--border-color);">
                                        <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit" title="Delete User"
                                                style="padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; border-radius: 8px;">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr style="background-color: var(--bg-card) !important;">
                                <td colspan="4"
                                    style="background-color: var(--bg-card) !important; text-align: center; padding: 3rem 1rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color);">
                                    <i class="fas fa-inbox"
                                        style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 1rem;"></i>
                                    <p>Belum ada guru terdaftar</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Jam + Tanggal realtime
        function updateClock() {
            const now = new Date();

            // Format jam HH:MM:SS
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };

            // Menggunakan replace untuk memastikan separator adalah titik dua (:) 
            // karena locale id-ID seringkali menggunakan titik (.)
            document.getElementById('clock').innerText = now.toLocaleTimeString('id-ID', timeOptions).replace(/\./g, ':');

            // Format tanggal lengkap (Hari, Tanggal Bulan Tahun)
            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('date').innerText = now.toLocaleDateString('id-ID', dateOptions);
        }

        // Greeting otomatis
        function updateGreeting() {
            const now = new Date();
            const hour = now.getHours();
            let greeting;

            if (hour >= 4 && hour < 11) {
                greeting = "Selamat pagi";
            } else if (hour >= 11 && hour < 15) {
                greeting = "Selamat siang";
            } else if (hour >= 15 && hour < 18) {
                greeting = "Selamat sore";
            } else {
                greeting = "Selamat malam";
            }

            document.getElementById('greeting').innerText = `${greeting}, Admin!`;
        }

        // Get responsive width for mobile
        function getPopupWidth() {
            if (window.innerWidth <= 480) {
                return '95vw';
            } else if (window.innerWidth <= 768) {
                return '90vw';
            } else {
                return '700px';
            }
        }

    // Popup untuk menampilkan daftar guru
        function showTeachersPopup() {
            const teachersData = @json($users);
            const totalTeachers = teachersData.length;

            let teacherContent = `
                <div style="text-align: left; max-height: 70vh; overflow-y: auto; padding: 10px 0;">
                    <h4 style="margin-bottom: 15px; color: var(--accent-blue);"><i data-feather="users" style="width: 20px; height: 20px; margin-right: 8px; display: inline;"></i>Daftar Guru (Total: ${totalTeachers})</h4>
                    <ul style="margin: 0; padding-left: 20px; list-style-type: none;">
            `;

            teachersData.forEach((teacher, index) => {
                teacherContent += `
                    <li style="padding: 8px 0; color: var(--text-main); font-size: 0.95rem; border-bottom: 1px solid var(--border-color);">
                        <span style="display: inline-block; width: 20px; text-align: center; color: var(--accent-blue); font-size: 0.9rem; font-weight: 600;">${index + 1}.</span>
                        ${teacher.name}
                    </li>
                `;
            });

            teacherContent += `
                    </ul>
                </div>
            `;

            Swal.fire({
                title: '<i data-feather="users" style="width: 24px; height: 24px; margin-right: 8px; display: inline;"></i>Daftar Guru',
                html: teacherContent,
                icon: 'info',
                confirmButtonText: 'Tutup',
                confirmButtonColor: 'var(--accent-blue)',
                width: getPopupWidth(),
                padding: '20px',
                scrollbarPadding: false,
                didOpen: (modal) => {
                    const title = modal.querySelector('.swal2-title');
                    if (title) {
                        title.style.fontSize = window.innerWidth <= 480 ? '1.2rem' : '1.5rem';
                    }
                    feather.replace();
                }
            });
        }

        // Popup untuk menampilkan absensi hari ini
        function showAbsenciPopup() {
            const today = new Date();
            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const todayDate = today.toLocaleDateString('id-ID', dateOptions);
            const absenciCount = {{ $absensiCount }};
            const currentKey = '{{ $currentKey }}';

            let html = `
                <div style="text-align: center;">
                    <div style="margin-bottom: 20px;">
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0 0 10px 0;">Tanggal:</p>
                        <h3 style="margin: 0 0 20px 0; color: var(--accent-green);">${todayDate}</h3>
                    </div>
                    <div style="background: rgba(16, 185, 129, 0.1); padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 2px solid var(--accent-green);">
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Total Absensi</p>
                        <h2 style="margin: 0; color: var(--accent-green); font-size: 2.5rem; font-weight: 800;">${absenciCount}</h2>
                    </div>
                    <a href="/backup?month=${currentKey}" class="btn btn-success" style="display: inline-block; padding: 10px 20px; background: var(--accent-green); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-chart-bar"></i> Lihat Backup Panel
                    </a>
                </div>
            `;

            Swal.fire({
                title: '<i data-feather="bar-chart-2" style="width: 24px; height: 24px; margin-right: 8px; display: inline;"></i>Absensi',
                html: html,
                icon: 'info',
                confirmButtonText: 'Tutup',
                confirmButtonColor: 'var(--accent-green)',
                width: getPopupWidth(),
                padding: '20px',
                scrollbarPadding: false,
                didOpen: (modal) => {
                    const title = modal.querySelector('.swal2-title');
                    if (title) {
                        title.style.fontSize = window.innerWidth <= 480 ? '1.2rem' : '1.5rem';
                    }
                    feather.replace();
                }
            });
        }

        // Popup untuk menampilkan daftar kelas
        function showClassesPopup() {
            const classesData = @json($studentsByClass);
            const filteredClasses = Object.fromEntries(
                Object.entries(classesData).filter(([key]) => key !== 'Kelas')
            );
            const totalClasses = Object.keys(filteredClasses).length;

            let classContent = `
                <div style="text-align: left; max-height: 70vh; overflow-y: auto; padding: 10px 0;">
                    <h4 style="margin-bottom: 15px; color: var(--accent-info);"><i data-feather="book-open" style="width: 20px; height: 20px; margin-right: 8px; display: inline;"></i>Daftar Kelas (Total: ${totalClasses})</h4>
            `;

            for (const [className, students] of Object.entries(filteredClasses)) {
                classContent += `
                    <div style="margin-bottom: 12px; padding: 12px; background: rgba(14, 165, 233, 0.1); border-radius: 8px; border-left: 4px solid var(--accent-info); font-size: 0.95rem;">
                        <strong style="color: var(--accent-info); font-size: 1rem;"><i data-feather="home" style="width: 18px; height: 18px; margin-right: 6px; display: inline;"></i>${className}</strong>
                        <p style="margin: 5px 0; color: var(--text-muted); font-size: 0.9rem;">${students.length} siswa</p>
                    </div>
                `;
            }

            classContent += `</div>`;

            Swal.fire({
                title: '<i data-feather="bar-chart-2" style="width: 24px; height: 24px; margin-right: 8px; display: inline;"></i>Ringkasan Kelas',
                html: classContent,
                icon: 'info',
                confirmButtonText: 'Tutup',
                confirmButtonColor: 'var(--accent-info)',
                width: getPopupWidth(),
                padding: '20px',
                scrollbarPadding: false,
                didOpen: (modal) => {
                    const title = modal.querySelector('.swal2-title');
                    if (title) {
                        title.style.fontSize = window.innerWidth <= 480 ? '1.2rem' : '1.5rem';
                    }
                    feather.replace();
                }
            });
        }

        // Popup untuk menampilkan detail siswa per kelas
        function showStudentsPopup() {
            const classesData = @json($studentsByClass);
            const filteredClasses = Object.fromEntries(
                Object.entries(classesData).filter(([key]) => key !== 'Kelas')
            );
            const totalStudents = Object.values(filteredClasses).flat().filter(s => s.name !== 'Nama').length;

            let html = `
                <div style="text-align: left; max-height: 70vh; overflow-y: auto; padding: 10px 0;">
                    <h4 style="margin-bottom: 15px; color: var(--accent-warning);"><i data-feather="users" style="width: 20px; height: 20px; margin-right: 8px; display: inline;"></i>Daftar Siswa (Total: ${totalStudents})</h4>
            `;

            for (const [className, students] of Object.entries(filteredClasses)) {
                const filteredStudents = students.filter(s => s.name !== 'Nama');
                html += `
                    <div style="margin-bottom: 15px; padding: 12px; background: rgba(245, 158, 11, 0.1); border-radius: 8px; border-left: 4px solid var(--accent-warning);">
                        <strong style="color: var(--accent-warning); font-size: 0.95rem;"><i data-feather="clipboard" style="width: 18px; height: 18px; margin-right: 6px; display: inline;"></i>Kelas ${className}</strong>
                        <p style="margin: 8px 0 10px 0; color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">Siswa (${filteredStudents.length}):</p>
                        <ul style="margin: 0; padding-left: 18px; list-style-type: none;">
                `;

                filteredStudents.forEach((student, index) => {
                    html += `
                        <li style="padding: 4px 0; color: var(--text-main); font-size: 0.9rem; word-break: break-word;">
                            <span style="display: inline-block; width: 16px; text-align: center; color: var(--accent-warning); font-size: 0.8rem;">●</span>
                            ${student.name}
                        </li>
                    `;
                });

                html += `
                        </ul>
                    </div>
                `;
            }

            html += `</div>`;

            Swal.fire({
                title: '<i data-feather="book-open" style="width: 24px; height: 24px; margin-right: 8px; display: inline;"></i>Detail Siswa Per Kelas',
                html: html,
                icon: 'info',
                confirmButtonText: 'Tutup',
                confirmButtonColor: 'var(--accent-warning)',
                width: getPopupWidth(),
                padding: '20px',
                scrollbarPadding: false,
                didOpen: (modal) => {
                    const title = modal.querySelector('.swal2-title');
                    if (title) {
                        title.style.fontSize = window.innerWidth <= 480 ? '1.2rem' : '1.5rem';
                    }
                    feather.replace();
                }
            });
        }

        setInterval(updateClock, 1000);
        updateClock();
        updateGreeting();
    </script>

@endsection
