<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Verifikasi Email - {{ $site_settings['site_name'] ?? 'Konut Update' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0F172A;
            padding: 24px;
        }
        .verify-card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 18px;
            padding: 40px 36px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.35);
            text-align: center;
        }
        .verify-icon {
            width: 72px; height: 72px; margin: 0 auto 20px;
            border-radius: 50%;
            background: #F0FDF4;
            display: flex; align-items: center; justify-content: center;
            color: var(--primary, #189B39);
        }
        .verify-card h1 { font-size: 1.35rem; font-weight: 800; color: #1F2937; margin-bottom: 10px; letter-spacing: -0.02em; }
        .verify-card p { color: #6B7280; font-size: 0.9rem; line-height: 1.65; margin-bottom: 8px; }
        .verify-email {
            display: inline-block; font-weight: 700; color: #374151;
            background: #F3F4F6; border-radius: 8px; padding: 4px 12px; margin-bottom: 20px;
        }
        .btn-verify {
            display: inline-block; padding: 12px 28px; background: var(--primary, #189B39); color: #fff;
            border: none; border-radius: 10px; font-size: 0.9rem; font-weight: 700; font-family: inherit;
            cursor: pointer; text-decoration: none; transition: all 0.2s;
        }
        .btn-verify:hover { background: var(--primary-hover, #147A2E); transform: translateY(-1px); }
        .btn-secondary {
            display: inline-block; margin-top: 14px; padding: 11px 24px; background: #fff; color: #6B7280;
            border: 1.5px solid #E5E7EB; border-radius: 10px; font-size: 0.85rem; font-weight: 600; font-family: inherit;
            cursor: pointer; text-decoration: none; transition: all 0.2s;
        }
        .btn-secondary:hover { border-color: #D1D5DB; color: #374151; }
        .status-ok {
            background: #F0FDF4; border: 1px solid #BBF7D0; color: #16A34A;
            padding: 12px 14px; border-radius: 10px; font-size: 0.85rem; margin-bottom: 20px;
        }
        .auth-footer { margin-top: 24px; font-size: 0.75rem; color: #9CA3AF; }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="verify-icon">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4Z"/></svg>
        </div>
        <h1>Verifikasi Alamat Email</h1>
        <p>Sebelum melanjutkan, periksa email Anda untuk link verifikasi.</p>
        <p>Belum menerima email?</p>
        <div class="verify-email">{{ auth()->user()->email }}</div>

        @if (session('status'))
            <div class="status-ok">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-verify">Kirim Ulang Email Verifikasi</button>
        </form>
        <a href="{{ route('home') }}" class="btn-secondary">Kembali ke Beranda</a>

        <div class="auth-footer">
            &copy; {{ date('Y') }} <strong>{{ $site_settings['site_name'] ?? 'Konut Update' }}</strong>. All rights reserved.
        </div>
    </div>
</body>
</html>
