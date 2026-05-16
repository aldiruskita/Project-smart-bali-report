@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="auth-wrapper">
    {{-- LEFT: Branding Panel --}}
    <div class="auth-brand-panel">
        <div class="auth-brand-bg">
            {{-- Animated decorative SVG ornaments --}}
            <svg class="auth-ornament auth-ornament--top" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" stroke="rgba(212,175,55,0.25)" stroke-width="1.5"/>
                <circle cx="100" cy="100" r="60" stroke="rgba(212,175,55,0.15)" stroke-width="1"/>
                <circle cx="100" cy="100" r="40" stroke="rgba(212,175,55,0.1)" stroke-width="1"/>
                <path d="M100 20 L105 35 L100 30 L95 35Z" fill="rgba(212,175,55,0.3)"/>
                <path d="M100 180 L105 165 L100 170 L95 165Z" fill="rgba(212,175,55,0.3)"/>
                <path d="M20 100 L35 95 L30 100 L35 105Z" fill="rgba(212,175,55,0.3)"/>
                <path d="M180 100 L165 95 L170 100 L165 105Z" fill="rgba(212,175,55,0.3)"/>
            </svg>
            <svg class="auth-ornament auth-ornament--bottom" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="90" stroke="rgba(255,255,255,0.08)" stroke-width="1" stroke-dasharray="8 6"/>
                <circle cx="100" cy="100" r="50" stroke="rgba(255,255,255,0.05)" stroke-width="1" stroke-dasharray="4 4"/>
            </svg>
            {{-- Floating particles --}}
            <div class="auth-particles">
                <span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
        <div class="auth-brand-content">
            <div class="auth-brand-logo">
                <img src="{{ asset('images/logo-icon.png') }}" alt="SBR Logo">
            </div>
            <h2 class="auth-brand-title">Smart Bali Report</h2>
            <p class="auth-brand-tagline">Lapor Cepat, Bali Hebat</p>
            <div class="auth-brand-divider"></div>
            <p class="auth-brand-desc">Platform pelaporan masalah lingkungan berbasis web untuk mewujudkan Bali yang lebih baik.</p>
            <div class="auth-brand-features">
                <div class="auth-feature">
                    <span class="material-symbols-rounded">speed</span>
                    <span>Laporan Cepat & Mudah</span>
                </div>
                <div class="auth-feature">
                    <span class="material-symbols-rounded">track_changes</span>
                    <span>Pantau Status Real-time</span>
                </div>
                <div class="auth-feature">
                    <span class="material-symbols-rounded">groups</span>
                    <span>Komunitas Peduli Lingkungan</span>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Login Form --}}
    <div class="auth-form-panel">
        <div class="auth-form-container">
            {{-- Mobile logo (hidden on desktop) --}}
            <div class="auth-mobile-logo">
                <img src="{{ asset('images/logo-icon.png') }}" alt="SBR">
                <span>SBR</span>
            </div>

            <div class="auth-form-header">
                <div class="auth-form-icon">
                    <span class="material-symbols-rounded">waving_hand</span>
                </div>
                <h1 class="auth-form-title">Selamat Datang!</h1>
                <p class="auth-form-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            @if($errors->any())
                <div class="auth-alert auth-alert--error">
                    <span class="material-symbols-rounded">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="auth-form" id="loginForm">
                @csrf
                <div class="auth-input-group">
                    <div class="auth-input-wrapper">
                        <span class="material-symbols-rounded auth-input-icon">mail</span>
                        <input type="email" name="email" id="loginEmail" class="auth-input" value="{{ old('email') }}" placeholder=" " required autofocus>
                        <label for="loginEmail" class="auth-input-label">Alamat Email</label>
                    </div>
                </div>

                <div class="auth-input-group">
                    <div class="auth-input-wrapper">
                        <span class="material-symbols-rounded auth-input-icon">lock</span>
                        <input type="password" name="password" id="loginPassword" class="auth-input" placeholder=" " required>
                        <label for="loginPassword" class="auth-input-label">Password</label>
                        <button type="button" class="auth-password-toggle" onclick="togglePassword('loginPassword', this)" aria-label="Tampilkan password">
                            <span class="material-symbols-rounded">visibility_off</span>
                        </button>
                    </div>
                </div>

                <div class="auth-options">
                    <label class="auth-checkbox">
                        <input type="checkbox" name="remember" id="remember">
                        <span class="auth-checkmark"></span>
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="auth-link">Lupa password?</a>
                </div>

                <button type="submit" class="auth-submit-btn" id="loginSubmitBtn">
                    <span class="auth-submit-text">
                        <span class="material-symbols-rounded">login</span>
                        Masuk
                    </span>
                    <span class="auth-submit-loader">
                        <svg viewBox="0 0 24 24" width="20" height="20"><circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" fill="none" stroke-dasharray="32" stroke-linecap="round"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="0.8s" repeatCount="indefinite"/></circle></svg>
                    </span>
                </button>
            </form>

            <div class="auth-divider">
                <span>atau</span>
            </div>

            <div class="auth-switch">
                Belum punya akun?
                <a href="{{ route('register') }}" class="auth-switch-link">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('.material-symbols-rounded');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility_off';
    }
}

document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginSubmitBtn');
    btn.classList.add('loading');
    btn.disabled = true;
});
</script>
@endpush
@endsection
