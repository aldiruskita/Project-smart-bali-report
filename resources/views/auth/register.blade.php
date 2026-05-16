@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="auth-wrapper">
    {{-- LEFT: Branding Panel --}}
    <div class="auth-brand-panel">
        <div class="auth-brand-bg">
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
            <p class="auth-brand-desc">Bergabung dengan ribuan warga yang peduli. Bersama kita bangun Bali yang lebih bersih dan nyaman.</p>
            <div class="auth-brand-features">
                <div class="auth-feature">
                    <span class="material-symbols-rounded">verified_user</span>
                    <span>Akun Aman & Terverifikasi</span>
                </div>
                <div class="auth-feature">
                    <span class="material-symbols-rounded">notifications_active</span>
                    <span>Notifikasi Update Laporan</span>
                </div>
                <div class="auth-feature">
                    <span class="material-symbols-rounded">emoji_events</span>
                    <span>Kontribusi untuk Bali Hebat</span>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Register Form --}}
    <div class="auth-form-panel">
        <div class="auth-form-container">
            {{-- Mobile logo --}}
            <div class="auth-mobile-logo">
                <img src="{{ asset('images/logo-icon.png') }}" alt="SBR">
                <span>SBR</span>
            </div>

            <div class="auth-form-header">
                <div class="auth-form-icon">
                    <span class="material-symbols-rounded">person_add</span>
                </div>
                <h1 class="auth-form-title">Buat Akun Baru</h1>
                <p class="auth-form-subtitle">Daftar gratis dan mulai laporkan masalah di sekitarmu</p>
            </div>

            @if($errors->any())
                <div class="auth-alert auth-alert--error">
                    <span class="material-symbols-rounded">error</span>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
                @csrf

                <div class="auth-input-group">
                    <div class="auth-input-wrapper">
                        <span class="material-symbols-rounded auth-input-icon">person</span>
                        <input type="text" name="name" id="regName" class="auth-input" value="{{ old('name') }}" placeholder=" " required autofocus>
                        <label for="regName" class="auth-input-label">Nama Lengkap</label>
                    </div>
                </div>

                <div class="auth-input-group">
                    <div class="auth-input-wrapper">
                        <span class="material-symbols-rounded auth-input-icon">mail</span>
                        <input type="email" name="email" id="regEmail" class="auth-input" value="{{ old('email') }}" placeholder=" " required>
                        <label for="regEmail" class="auth-input-label">Alamat Email</label>
                    </div>
                </div>

                <div class="auth-input-group">
                    <div class="auth-input-wrapper">
                        <span class="material-symbols-rounded auth-input-icon">phone</span>
                        <input type="text" name="phone" id="regPhone" class="auth-input" value="{{ old('phone') }}" placeholder=" ">
                        <label for="regPhone" class="auth-input-label">No. Telepon <small style="opacity:0.6">(opsional)</small></label>
                    </div>
                </div>

                <div class="auth-input-row">
                    <div class="auth-input-group">
                        <div class="auth-input-wrapper">
                            <span class="material-symbols-rounded auth-input-icon">lock</span>
                            <input type="password" name="password" id="regPassword" class="auth-input" placeholder=" " required>
                            <label for="regPassword" class="auth-input-label">Password</label>
                            <button type="button" class="auth-password-toggle" onclick="togglePassword('regPassword', this)" aria-label="Tampilkan password">
                                <span class="material-symbols-rounded">visibility_off</span>
                            </button>
                        </div>
                    </div>
                    <div class="auth-input-group">
                        <div class="auth-input-wrapper">
                            <span class="material-symbols-rounded auth-input-icon">lock_reset</span>
                            <input type="password" name="password_confirmation" id="regPasswordConfirm" class="auth-input" placeholder=" " required>
                            <label for="regPasswordConfirm" class="auth-input-label">Konfirmasi</label>
                            <button type="button" class="auth-password-toggle" onclick="togglePassword('regPasswordConfirm', this)" aria-label="Tampilkan password">
                                <span class="material-symbols-rounded">visibility_off</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Password strength indicator --}}
                <div class="auth-password-strength" id="passwordStrength" style="display:none;">
                    <div class="auth-strength-bar">
                        <div class="auth-strength-fill" id="strengthFill"></div>
                    </div>
                    <span class="auth-strength-text" id="strengthText"></span>
                </div>

                <div class="auth-terms-box">
                    <span class="material-symbols-rounded" style="color:var(--primary);flex-shrink:0;">shield</span>
                    <p>Dengan mendaftar, Anda menyetujui <a href="#">Syarat &amp; Ketentuan</a> serta <a href="#">Kebijakan Privasi</a> kami.</p>
                </div>

                <button type="submit" class="auth-submit-btn" id="registerSubmitBtn">
                    <span class="auth-submit-text">
                        <span class="material-symbols-rounded">how_to_reg</span>
                        Daftar Sekarang
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
                Sudah punya akun?
                <a href="{{ route('login') }}" class="auth-switch-link">Masuk Sekarang</a>
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

// Password strength checker
const passwordInput = document.getElementById('regPassword');
const strengthBar = document.getElementById('passwordStrength');
const strengthFill = document.getElementById('strengthFill');
const strengthText = document.getElementById('strengthText');

if (passwordInput) {
    passwordInput.addEventListener('input', function() {
        const val = this.value;
        if (val.length === 0) {
            strengthBar.style.display = 'none';
            return;
        }
        strengthBar.style.display = 'flex';
        let score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { width: '20%', color: '#ef4444', text: 'Sangat Lemah' },
            { width: '40%', color: '#f59e0b', text: 'Lemah' },
            { width: '60%', color: '#eab308', text: 'Cukup' },
            { width: '80%', color: '#22c55e', text: 'Kuat' },
            { width: '100%', color: '#16a34a', text: 'Sangat Kuat' }
        ];
        const level = levels[Math.min(score, 4)];
        strengthFill.style.width = level.width;
        strengthFill.style.background = level.color;
        strengthText.textContent = level.text;
        strengthText.style.color = level.color;
    });
}

document.getElementById('registerForm').addEventListener('submit', function() {
    const btn = document.getElementById('registerSubmitBtn');
    btn.classList.add('loading');
    btn.disabled = true;
});
</script>
@endpush
@endsection
