<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <!-- CDN Libraries -->
    @include('layouts.cdn')

    <!-- Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --bg-body: #0f172a;
            --text-main: #f1f5f9;
        }

        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Abstract Background Decor for Dark Mode */
        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.05) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(37, 99, 235, 0.05) 0%, transparent 40%);
            z-index: -1;
        }

        #auth-container {
            width: 100%;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-animate {
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="bg-pattern"></div>
    <div id="auth-container">
        <div class="auth-animate" style="width: 100%; display: flex; justify-content: center;">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
