<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') – @yield('title') | KonutUpdate</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #189B39;
            --primary-hover: #147A2E;
            --primary-light: #E8F5E9;
            --bg: #F5F5F5;
            --surface: #FFFFFF;
            --text: #1F2937;
            --muted: #6B7280;
            --border: #E5E7EB;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --primary-light: #1A3A22;
                --bg: #0F172A;
                --surface: #1E293B;
                --text: #E2E8F0;
                --muted: #94A3B8;
                --border: #334155;
            }
        }
        html, body { height: 100%; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }
        .page { width: 100%; max-width: 520px; }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px 32px 32px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }
        .brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-decoration: none;
            color: var(--text);
            margin-bottom: 24px;
        }
        .brand .mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #fff;
            font-size: 15px;
            font-weight: 800;
        }
        .code {
            font-size: clamp(72px, 18vw, 128px);
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        h1 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .msg {
            font-size: 14px;
            line-height: 1.6;
            color: var(--muted);
            margin: 0 auto 28px;
            max-width: 380px;
        }
        .msg strong { color: var(--text); }
        .actions { display: flex; flex-direction: column; gap: 10px; }
        @media (min-width: 480px) {
            .actions { flex-direction: row; justify-content: center; }
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.15s, transform 0.15s, color 0.15s;
            border: 1px solid transparent;
            cursor: pointer;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-ghost {
            background: transparent;
            color: var(--text);
            border-color: var(--border);
        }
        .btn-ghost:hover { background: var(--primary-light); border-color: var(--primary); }
        .btn svg { width: 16px; height: 16px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <a class="brand" href="{{ url('/') }}">
                <span class="mark">K</span> KonutUpdate
            </a>
            <div class="code">@yield('code')</div>
            <h1>@yield('title')</h1>
            <p class="msg">@yield('message')</p>
            <div class="actions">
                <a class="btn btn-primary" href="{{ url('/') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/></svg>
                    Kembali ke Beranda
                </a>
                <button class="btn btn-ghost" type="button" onclick="history.length > 1 ? history.back() : (location.href = '/')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1018 0 9 9 0 00-18 0z"/></svg>
                    Kembali
                </button>
            </div>
        </div>
    </div>
</body>
</html>