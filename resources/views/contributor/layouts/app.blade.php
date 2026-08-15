<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Kontributor') - {{ $site_settings['site_name'] ?? 'Konut.Update' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @if(!empty($site_settings['primary_color']) || !empty($site_settings['accent_color']))
    <style>
        :root {
            @if(!empty($site_settings['primary_color']))
            --primary: {{ $site_settings['primary_color'] }};
            --primary-hover: {{ $site_settings['primary_color'] }}dd;
            --primary-light: {{ $site_settings['primary_color'] }}1a;
            --primary-container: {{ $site_settings['primary_color'] }}33;
            @endif
            @if(!empty($site_settings['accent_color']))
            --accent: {{ $site_settings['accent_color'] }};
            --accent-hover: {{ $site_settings['accent_color'] }}dd;
            --accent-light: {{ $site_settings['accent_color'] }}1a;
            @endif
        }
    </style>
    @endif
    @stack('styles')
</head>
<body>
    <div class="contributor-wrapper">
        <header class="contributor-topbar">
            <div class="contributor-topbar-inner">
                <a href="{{ route('kontributor.dashboard') }}" class="contributor-brand">
                    @if(!empty($site_settings['logo']))
                        <img src="{{ Storage::url($site_settings['logo']) }}" alt="{{ $site_settings['site_name'] ?? 'Konut.Update' }}">
                    @else
                        <span class="brand-text"><span>K</span>onut.Update</span>
                    @endif
                    <span class="contributor-brand-badge">Kontributor</span>
                </a>
                <nav class="contributor-nav">
                    <a href="{{ route('kontributor.dashboard') }}" class="{{ request()->routeIs('kontributor.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="{{ route('kontributor.posts.create') }}" class="{{ request()->routeIs('kontributor.posts.create') ? 'active' : '' }}"><i class="bi bi-pencil-square"></i> Tulis Kiriman</a>
                    <a href="{{ url('/') }}" target="_blank"><i class="bi bi-globe"></i> Lihat Website</a>
                </nav>
                <div class="contributor-user">
                    <span class="contributor-user-name">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="contributor-logout" title="Logout"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="contributor-main">
            <div class="contributor-container">
                @if(session('success'))
                    <div class="alert-admin alert-admin-success">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert-admin alert-admin-danger">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert-admin alert-admin-danger">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <strong>Periksa kembali isian Anda:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>

        <footer class="contributor-footer">
            &copy; {{ date('Y') }} {{ $site_settings['site_name'] ?? 'Konut.Update' }} &mdash; Panel Kontributor
        </footer>
    </div>

    {{-- Toast Container --}}
    <div id="toastAdminContainer" class="toast-admin-container"></div>

    @stack('scripts')
</body>
</html>
