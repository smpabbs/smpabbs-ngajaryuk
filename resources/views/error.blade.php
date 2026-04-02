@extends('layouts.app')

@section('title', 'Error - NgajarYuk')

@section('content')
<style>
    .status-card {
        background: var(--bg-card);
        border: 1px solid rgba(239, 68, 68, 0.3);
        box-shadow: 0 10px 30px -10px rgba(239, 68, 68, 0.2);
        border-radius: var(--card-radius);
    }
    .status-icon-wrapper {
        width: 90px;
        height: 90px;
        background: rgba(239, 68, 68, 0.1);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }
    .status-icon-wrapper i {
        font-size: 3rem;
        color: #ef4444;
    }
    .btn-outline-custom {
        color: var(--text-main);
        border: 1px solid var(--border-color);
        background: transparent;
    }
    .btn-outline-custom:hover {
        background: rgba(255, 255, 255, 0.05);
        color: white;
    }
</style>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="status-card p-5 text-center animate__animated animate__zoomIn">
                <div class="status-icon-wrapper">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                
                <h1 class="fw-800 mb-3" style="color: #ef4444; font-size: 2.25rem;">ERROR</h1>
                <p class="mb-4" style="color: #94a3b8; font-size: 1.1rem;">
                    <strong>{{ session('error') ?? 'Terjadi kesalahan sistem.' }}</strong>
                </p>

                <div class="d-flex flex-column gap-3 justify-content-center mt-2">
                    <a href="{{ route('absensi.index') }}" class="btn btn-primary px-4 py-2" style="font-weight: 600; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <a href="/journal" class="btn btn-outline-custom px-4 py-2" style="font-weight: 600;">
                        <i class="fas fa-book me-2"></i>Ke Journal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
