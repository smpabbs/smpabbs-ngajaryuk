@extends('layouts.auth')

@section('title', 'Verifikasi Email - NgajarYuk')

@section('content')
    <style>
        .login-box {
            display: flex;
            width: 100%;
            max-width: 900px;
            background: #1e293b;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            min-height: 520px;
            border: 1px solid #334155;
        }

        .login-hero {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color, #3b82f6) 0%, var(--primary-hover, #2563eb) 100%);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-hero::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px;
            left: -100px;
        }

        .login-hero::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            bottom: -50px;
            right: -50px;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-logo-box {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .hero-logo-box img {
            width: 70px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.2));
        }

        .hero-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .hero-desc {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 500;
            max-width: 280px;
            margin: 0 auto;
        }

        .login-form-area {
            flex: 1;
            padding: 50px 45px;
            background: #1e293b;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #f8fafc;
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: #94a3b8;
            margin-bottom: 2.5rem;
            font-weight: 500;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color, #3b82f6) 0%, var(--primary-hover, #2563eb) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 1.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.5);
        }

        .alert-success-custom {
            background: rgba(16, 185, 129, 0.1);
            color: #34d399;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            border: 1px solid rgba(16, 185, 129, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .verification-message {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            border-radius: 12px;
            color: var(--text-main);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .login-box {
                flex-direction: column;
                max-width: 450px;
                min-height: auto;
            }

            .login-hero {
                padding: 40px 20px;
            }

            .login-form-area {
                padding: 40px 30px;
            }

            .hero-title {
                font-size: 1.5rem;
            }

            .hero-logo-box {
                width: 80px;
                height: 80px;
                margin-bottom: 1.5rem;
            }

            .hero-logo-box img {
                width: 55px;
            }
        }
    </style>

    <div class="login-box">
        {{-- HERO SIDE --}}
        <div class="login-hero">
            <div class="hero-content">
                <div class="hero-logo-box">
                    <img src="{{ asset('abbs.png') }}" alt="SMP ABBS">
                </div>
                <h1 class="hero-title">Verifikasi Email</h1>
                <p class="hero-desc">Satu langkah lagi untuk menyelesaikan pendaftaran Anda</p>
            </div>
        </div>

        {{-- FORM SIDE --}}
        <div class="login-form-area">
            <div class="form-header">
                <h2 class="form-title">Cek Inbox Anda</h2>
                <p class="form-subtitle">Tautan verifikasi telah dikirimkan.</p>
            </div>

            @if (session('resent'))
                <div class="alert-success-custom">
                    <i class="fas fa-check-circle"></i>
                    <span>Tautan verifikasi baru telah dikirim ke alamat email Anda.</span>
                </div>
            @endif

            <div class="verification-message">
                Sebelum melanjutkan, harap periksa email Anda untuk mengklik tautan verifikasi. <br><br>
                Jika Anda tidak menerima email tersebut, klik tombol di bawah untuk meminta tautan baru.
            </div>

            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn-submit">
                    <span>KIRIM ULANG TAUTAN</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
@endsection
