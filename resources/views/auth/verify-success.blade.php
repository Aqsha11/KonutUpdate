<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Verifikasi Email Terkirim - {{ $site_settings['site_name'] ?? 'Konut.Update' }}</title>
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
            max-width: 460px;
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
        .verify-steps {
            text-align: left;
            background: #F9FAFB;
            border: 1px solid #F3F4F6;
            border-radius: 12px;
            padding: 16px 20px;
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .verify-step { display: flex; align-items: flex-start; gap: 10px; font-size: 0.85rem; color: #4B5563; line-height: 1.5; }
        .verify-step .step-num {
            width: 24px; height: 24px; border-radius: 50%;
            background: var(--primary, #189B39); color: #fff;
            font-size: 0.75rem; font-weight: 800;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
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
        .note {
            margin-top: 18px; font-size: 0.78rem; color: #9CA3AF;
            background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E;
            padding: 10px 14px; border-radius: 10px; line-height: 1.5;
        }
        .countdown-box {
            margin-top: 18px;
            background: #F0FDF4; border: 1px solid #BBF7D0;
            border-radius: 12px; padding: 18px 16px;
            text-align: center;
        }
        .countdown-label { font-size: 0.82rem; color: #166534; font-weight: 700; margin-bottom: 10px; }
        .countdown-timer {
            font-size: 2.5rem; font-weight: 800; color: var(--primary, #189B39);
            font-variant-numeric: tabular-nums; letter-spacing: 0.05em; line-height: 1;
        }
        .cd-sep { margin: 0 5px; }
        .countdown-warn { margin-top: 10px; font-size: 0.72rem; color: #4B5563; line-height: 1.5; }
        .expired-note {
            font-size: 0.85rem; color: #B45309; background: #FFFBEB;
            border: 1px solid #FDE68A; border-radius: 10px;
            padding: 12px 14px; line-height: 1.5;
        }
        .resend-box { margin-top: 18px; text-align: left; }
        .resend-box h3 { font-size: 0.85rem; font-weight: 800; color: #374151; margin-bottom: 10px; }
        .resend-box .form-group { margin-bottom: 12px; }
        .resend-box .form-group label { display: block; font-size: 0.78rem; font-weight: 600; color: #6B7280; margin-bottom: 5px; }
        .resend-box .form-input {
            width: 100%; padding: 10px 12px;
            border: 1.5px solid #E5E7EB; border-radius: 8px;
            font-size: 0.85rem; font-family: inherit;
            transition: all 0.2s; outline: none; background: #F9FAFB;
        }
        .resend-box .form-input:focus { border-color: var(--primary, #189B39); background: #fff; box-shadow: 0 0 0 3px rgba(24,155,57,0.1); }
        .btn-resend {
            width: 100%; padding: 11px;
            background: #fff; color: var(--primary, #189B39);
            border: 1.5px solid var(--primary, #189B39); border-radius: 8px;
            font-size: 0.85rem; font-weight: 700; font-family: inherit;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-resend:hover { background: var(--primary, #189B39); color: #fff; }
        .status-ok {
            background: #F0FDF4; border: 1px solid #BBF7D0; color: #16A34A;
            padding: 12px 14px; border-radius: 10px; font-size: 0.82rem; margin-bottom: 16px; line-height: 1.5;
        }
        .auth-footer { margin-top: 24px; font-size: 0.75rem; color: #9CA3AF; }
    </style>
</head>
<body>
    @php
        $sentAt = (int) session('verification_sent_at', 0);
        $remainingSeconds = $sentAt > 0 ? max(0, $sentAt + 60 - now()->getTimestamp()) : null;
    @endphp
    <div class="verify-card">
        <div class="verify-icon">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
        </div>
        <h1>Email Verifikasi Terkirim</h1>
        <p>Akun Anda berhasil dibuat. Kami telah mengirim link verifikasi ke email yang Anda daftarkan.</p>

        <div class="verify-steps">
            <div class="verify-step">
                <div class="step-num">1</div>
                <span>Buka email dan klik <strong>link verifikasi</strong> yang kami kirimkan.</span>
            </div>
            <div class="verify-step">
                <div class="step-num">2</div>
                <span>Email Anda terverifikasi dan Anda <strong>masuk otomatis</strong> ke akun.</span>
            </div>
            <div class="verify-step">
                <div class="step-num">3</div>
                <span>Anda siap mulai berkontribusi di {{ $site_settings['site_name'] ?? 'Konut.Update' }}.</span>
            </div>
        </div>

        <a href="{{ route('home') }}" class="btn-verify">Ke Beranda</a>
        <br>
        <a href="{{ route('login') }}" class="btn-secondary">Halaman Masuk</a>

        @if ($remainingSeconds !== null)
        <div class="countdown-box" x-data="{
            remaining: {{ $remainingSeconds }},
            init() {
                setInterval(() => { if (this.remaining > 0) this.remaining--; }, 1000);
            },
            get minutes() { return String(Math.floor(this.remaining / 60)).padStart(2, '0'); },
            get seconds() { return String(this.remaining % 60).padStart(2, '0'); }
        }">
            <template x-if="remaining > 0">
                <div>
                    <p class="countdown-label">Link verifikasi kedaluwarsa dalam</p>
                    <div class="countdown-timer">
                        <span x-text="minutes"></span><span class="cd-sep">:</span><span x-text="seconds"></span>
                    </div>
                    <p class="countdown-warn">Segera buka email Anda dan klik link verifikasi. Link hanya berlaku 1 menit.</p>
                </div>
            </template>
            <template x-if="remaining <= 0">
                <div class="expired-note">
                    <strong>Link telah kedaluwarsa.</strong> Silakan kirim ulang email verifikasi di bawah ini.
                </div>
            </template>
        </div>
        @else
        <div class="note">
            <strong>Belum menerima email?</strong> Periksa folder spam/junk, atau kirim ulang link verifikasi di bawah ini.
        </div>
        @endif

        @if (session('status'))
            <div class="status-ok">✓ {{ session('status') }}</div>
        @endif

        <div class="resend-box">
            <h3>Kirim Ulang Link Verifikasi</h3>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-input @error('email') is-invalid @enderror" placeholder="nama@email.com" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                    <div style="color:#EF4444;font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    @include('partials.turnstile')
                    @error('cf-turnstile-response')
                    <div style="color:#EF4444;font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn-resend">Kirim Ulang Email Verifikasi</button>
            </form>
        </div>

        <div class="auth-footer">
            &copy; {{ date('Y') }} <strong>{{ $site_settings['site_name'] ?? 'Konut.Update' }}</strong>. All rights reserved.
        </div>
    </div>
</body>
</html>
