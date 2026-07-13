<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Masuk - {{ $site_settings['site_name'] ?? 'Konut.Update' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0F172A;
            overflow: hidden;
        }
        .login-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        .login-brand {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            position: relative;
            overflow: hidden;
        }
        .login-brand::before {
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
        .login-brand-content {
            position: relative;
            z-index: 1;
            max-width: 480px;
        }
        .login-brand-logo { margin-bottom: 32px; }
        .login-brand-logo img { height: 48px; width: auto; }
        .login-brand-logo .brand-text { color: #fff; font-size: 2rem; font-weight: 900; letter-spacing: -0.03em; }
        .login-brand-logo .brand-text span { color: var(--accent, #F58220); }
        .login-brand h1 {
            color: #fff;
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 16px;
            letter-spacing: -0.03em;
        }
        .login-brand h1 span { color: var(--primary, #189B39); }
        .login-brand p {
            color: rgba(255,255,255,0.5);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 40px;
        }
        .login-brand-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .login-brand-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }
        .login-brand-feature .feature-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(24,155,57,0.15);
            color: var(--primary, #189B39);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .login-form-wrapper {
            width: 480px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            background: #fff;
            position: relative;
        }
        .login-form-inner { width: 100%; max-width: 360px; margin: 0 auto; }
        .login-form-header { margin-bottom: 32px; }
        .login-form-header h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1F2937;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }
        .login-form-header p { color: #6B7280; font-size: 0.9rem; }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #E5E7EB;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.2s;
            outline: none;
            background: #F9FAFB;
        }
        .form-input:focus {
            border-color: var(--primary, #189B39);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(24,155,57,0.1);
        }
        .form-input.is-invalid { border-color: #EF4444; }
        .form-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
        .password-wrapper { position: relative; }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            padding: 4px;
            display: flex;
            transition: color 0.2s;
        }
        .password-toggle:hover { color: #374151; }
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .form-check-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .form-check-custom input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1.5px solid #D1D5DB;
            accent-color: var(--primary, #189B39);
        }
        .form-check-custom span {
            font-size: 0.82rem;
            color: #6B7280;
        }
        .btn-login {
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
        }
        .btn-login:hover { background: var(--primary-hover, #147A2E); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(24,155,57,0.3); }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { opacity: 0.7; cursor: not-allowed; }
        .login-error {
            padding: 12px 14px;
            background: #FEF2F2;
            border: 1px solid #FED7D7;
            border-radius: 10px;
            color: #EF4444;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .login-footer {
            margin-top: 32px;
            text-align: center;
            font-size: 0.78rem;
            color: #9CA3AF;
        }
        .login-footer strong { color: #6B7280; }
        @media (max-width: 900px) {
            .login-brand { display: none; }
            .login-form-wrapper { width: 100%; padding: 40px 24px; }
            .login-form-inner { max-width: 400px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-brand">
            <div class="login-brand-content">
                <div class="login-brand-logo">
                    @if(!empty($site_settings['logo']))
                        <img src="{{ Storage::url($site_settings['logo']) }}" alt="{{ $site_settings['site_name'] ?? 'Konut.Update' }}">
                    @else
                        <div class="brand-text"><span>K</span>onut.Update</div>
                    @endif
                </div>
                <h1>Portal Admin<br><span>{{ $site_settings['site_name'] ?? 'Konut.Update' }}</span></h1>
                <p>Kelola berita, kategori, tag, halaman statis, dan pengaturan website dari satu panel kontrol terpadu.</p>
                <div class="login-brand-features">
                    <div class="login-brand-feature">
                        <div class="feature-icon">📰</div>
                        <span>Kelola berita dengan mudah</span>
                    </div>
                    <div class="login-brand-feature">
                        <div class="feature-icon">📊</div>
                        <span>Pantau performa dengan statistik real-time</span>
                    </div>
                    <div class="login-brand-feature">
                        <div class="feature-icon">🎨</div>
                        <span>Kustomisasi tampilan portal</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="login-form-wrapper">
            <div class="login-form-inner">
                <div class="login-form-header">
                    <h2>Selamat Datang</h2>
                    <p>Masuk untuk mengakses panel admin</p>
                </div>

                @if ($errors->any())
                    <div class="login-error">
                        <span>✕</span>
                        {{ $errors->first('email') }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="login-error" style="background:#F0FDF4;border-color:#BBF7D0;color:#16A34A;">
                        <span>✓</span>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-input @error('email') is-invalid @enderror" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-input @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                            <button type="button" class="password-toggle" onclick="togglePassword()" tabindex="-1">
                                <svg id="passwordIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="form-check-custom">
                            <input type="checkbox" name="remember">
                            <span>Ingat saya</span>
                        </label>
                    </div>
                    <button type="submit" class="btn-login" id="submitBtn">
                        Masuk ke Admin
                    </button>
                </form>

                <div class="login-footer">
                    &copy; {{ date('Y') }} <strong>{{ $site_settings['site_name'] ?? 'Konut.Update' }}</strong>. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var input = document.getElementById('password');
            var icon = document.getElementById('passwordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
        document.getElementById('loginForm').addEventListener('submit', function() {
            var btn = document.getElementById('submitBtn');
            btn.innerHTML = '<svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" style="animation:spin 0.8s linear infinite"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"/><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor" opacity="0.75"/></svg> Memproses...';
            btn.disabled = true;
        });
    </script>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        .animate-spin { animation: spin 0.8s linear infinite; }
    </style>
</body>
</html>
