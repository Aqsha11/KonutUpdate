<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Daftar Akun - {{ $site_settings['site_name'] ?? 'Konut Update' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0F172A;
        }
        .register-container { display: flex; width: 100%; min-height: 100vh; }
        .register-brand {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            position: relative;
            overflow: hidden;
        }
        .register-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(24,155,57,0.08) 0%, transparent 60%),
                        radial-gradient(circle at 70% 50%, rgba(245,130,32,0.06) 0%, transparent 60%);
            animation: brandGlow 8s ease-in-out infinite alternate;
        }
        @keyframes brandGlow {
            0% { transform: translate(0, 0); }
            100% { transform: translate(-2%, -2%); }
        }
        .register-brand-content { position: relative; z-index: 1; max-width: 480px; }
        .register-brand-logo { margin-bottom: 32px; }
        .register-brand-logo img { height: 48px; width: auto; }
        .register-brand-logo .brand-text { color: #fff; font-size: 2rem; font-weight: 900; letter-spacing: -0.03em; }
        .register-brand-logo .brand-text span { color: var(--accent, #F58220); }
        .register-brand h1 {
            color: #fff;
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 16px;
            letter-spacing: -0.03em;
        }
        .register-brand h1 span { color: var(--primary, #189B39); }
        .register-brand p {
            color: rgba(255,255,255,0.5);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 40px;
        }
        .register-brand-features { display: flex; flex-direction: column; gap: 16px; }
        .register-brand-feature { display: flex; align-items: center; gap: 12px; color: rgba(255,255,255,0.6); font-size: 0.9rem; }
        .register-brand-feature .feature-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(24,155,57,0.15);
            color: var(--primary, #189B39);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .register-form-wrapper {
            width: 520px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            background: #fff;
        }
        .register-form-inner { width: 100%; max-width: 400px; margin: 0 auto; }
        .register-form-header { margin-bottom: 28px; }
        .register-form-header h2 { font-size: 1.6rem; font-weight: 800; color: #1F2937; margin-bottom: 8px; letter-spacing: -0.02em; }
        .register-form-header p { color: #6B7280; font-size: 0.9rem; line-height: 1.6; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            display: flex;
            pointer-events: none;
        }
        .form-input {
            width: 100%;
            padding: 12px 42px;
            border: 1.5px solid #E5E7EB;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.2s;
            outline: none;
            background: #F9FAFB;
            color: #1F2937;
        }
        .form-input::placeholder { color: #B0B7C3; }
        .form-input:focus { border-color: var(--primary, #189B39); background: #fff; box-shadow: 0 0 0 3px rgba(24,155,57,0.1); }
        .form-input.is-invalid { border-color: #EF4444; }
        .form-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            padding: 6px;
            display: flex;
            transition: color 0.2s;
        }
        .password-toggle:hover { color: #374151; }
        .field-hint { font-size: 0.75rem; color: #9CA3AF; margin-top: 6px; }
        .field-error { color: #EF4444; font-size: 0.75rem; margin-top: 6px; }
        .btn-auth {
            width: 100%;
            padding: 13px;
            background: var(--primary, #189B39);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 4px;
        }
        .btn-auth:hover { background: var(--primary-hover, #147A2E); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(24,155,57,0.3); }
        .btn-auth:active { transform: translateY(0); }
        .btn-auth:disabled { opacity: 0.7; cursor: not-allowed; }
        .register-error {
            padding: 12px 14px;
            background: #FEF2F2;
            border: 1px solid #FED7D7;
            border-radius: 10px;
            color: #EF4444;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .register-error ul { margin: 0 0 0 18px; padding: 0; }
        .register-error ul li { margin-bottom: 2px; }
        .register-footer { margin-top: 28px; text-align: center; font-size: 0.78rem; color: #9CA3AF; }
        .register-footer strong { color: #6B7280; }
        .register-switch { margin-top: 20px; text-align: center; font-size: 0.85rem; color: #6B7280; }
        .register-switch a { color: var(--primary, #189B39); font-weight: 700; text-decoration: none; }
        .register-switch a:hover { text-decoration: underline; }
        @media (max-width: 900px) {
            .register-brand { display: none; }
            .register-form-wrapper { width: 100%; padding: 40px 24px; }
            .register-form-inner { max-width: 400px; }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-brand">
            <div class="register-brand-content">
                <div class="register-brand-logo">
                    @if(!empty($site_settings['logo']))
                        <img src="{{ Storage::url($site_settings['logo']) }}" alt="{{ $site_settings['site_name'] ?? 'Konut Update' }}">
                    @else
                        <div class="brand-text"><span>K</span>onut.Update</div>
                    @endif
                </div>
                <h1>Bergabung dengan<br><span>{{ $site_settings['site_name'] ?? 'Konut Update' }}</span></h1>
                <p>Daftar gratis untuk mulai membaca berita terkini dan menulis opini Anda.</p>
                <div class="register-brand-features">
                    <div class="register-brand-feature">
                        <div class="feature-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg></div>
                        <span>Verifikasi email untuk keamanan akun</span>
                    </div>
                    <div class="register-brand-feature">
                        <div class="feature-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></div>
                        <span>Kirim opini dan berita untuk ditayangkan</span>
                    </div>
                    <div class="register-brand-feature">
                        <div class="feature-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg></div>
                        <span>Notifikasi terbaru langsung ke email Anda</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="register-form-wrapper">
            <div class="register-form-inner">
                <div class="register-form-header">
                    <h2>Daftar Akun</h2>
                    <p>Isi data di bawah untuk membuat akun baru.</p>
                </div>

                @if ($errors->any())
                    <div class="register-error">
                        <span>✕</span>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <div class="input-wrap">
                            <span class="input-icon"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                            <input type="text" name="name" id="name" class="form-input @error('name') is-invalid @enderror" placeholder="Nama Anda" value="{{ old('name') }}" required autofocus autocomplete="name" maxlength="100">
                        </div>
                        @error('name')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <div class="input-wrap">
                            <span class="input-icon"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg></span>
                            <input type="email" name="email" id="email" class="form-input @error('email') is-invalid @enderror" placeholder="nama@email.com" value="{{ old('email') }}" required autocomplete="email" maxlength="191">
                        </div>
                        @error('email')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <div class="input-wrap">
                            <span class="input-icon"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                            <input type="password" name="password" id="password" class="form-input @error('password') is-invalid @enderror" placeholder="Min. 8 karakter" required autocomplete="new-password" minlength="8">
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" tabindex="-1" aria-label="Lihat kata sandi">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div class="field-hint">Minimal 8 karakter, harus ada huruf kapital & angka.</div>
                        @error('password')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <div class="input-wrap">
                            <span class="input-icon"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input @error('password') is-invalid @enderror" placeholder="Ulangi kata sandi" required autocomplete="new-password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)" tabindex="-1" aria-label="Lihat kata sandi">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        @include('partials.turnstile')
                    </div>
                    <button type="submit" class="btn-auth" id="submitBtn">
                        Daftar Sekarang
                    </button>
                </form>

                <div class="register-switch">
                    Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
                </div>
                <div class="register-footer">
                    &copy; {{ date('Y') }} <strong>{{ $site_settings['site_name'] ?? 'Konut Update' }}</strong>. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id, btn) {
            var input = document.getElementById(id);
            var icon = btn.querySelector('svg');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
        document.getElementById('registerForm').addEventListener('submit', function() {
            var btn = document.getElementById('submitBtn');
            btn.innerHTML = 'Memproses...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
