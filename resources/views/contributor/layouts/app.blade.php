<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Kontributor') - {{ $site_settings['site_name'] ?? 'Konut Update' }}</title>
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
    <div class="wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <nav class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                @if(!empty($site_settings['logo']))
                    <img src="{{ Storage::url($site_settings['logo']) }}" alt="{{ $site_settings['site_name'] ?? 'Konut Update' }}">
                @else
                    <div class="brand-text"><span>K</span>onut.Update</div>
                @endif
                <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Tutup sidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="sidebar-nav">
                <div class="nav-label">Menu Kontributor</div>
                <div class="nav-item">
                    <a href="{{ route('kontributor.dashboard') }}" class="nav-link {{ request()->routeIs('kontributor.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i> Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('kontributor.posts.create') }}" class="nav-link {{ request()->routeIs('kontributor.posts.create') ? 'active' : '' }}">
                        <i class="bi bi-pencil-square"></i> Tulis Kiriman
                    </a>
                </div>
            </div>
            <div class="sidebar-footer">
                <div class="user-info">
                    @if(auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="user-avatar">
                    @else
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    @endif
                    <div class="user-details">
                        <a href="{{ route('profile.index') }}" class="user-name">{{ auth()->user()->name }}</a>
                        <div class="user-role">Kontributor</div>
                    </div>
                </div>
                <a href="{{ route('logout') }}" class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-left"></i> Keluar
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
            </div>
        </nav>

        <div class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Buka menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="contributor-topbar-title">Panel Kontributor</span>
                </div>
                <div class="topbar-right">
                    <a href="{{ url('/') }}" target="_blank" class="btn-admin btn-admin-sm btn-admin-ghost d-none d-sm-inline-flex">
                        <i class="bi bi-globe"></i> Lihat Website
                    </a>
                    <a href="{{ route('logout') }}" class="contributor-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Keluar</span>
                    </a>
                </div>
            </div>

            <div class="content">
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

            <footer class="contributor-footer">
                &copy; {{ date('Y') }} {{ $site_settings['site_name'] ?? 'Konut Update' }} &mdash; Panel Kontributor
            </footer>
        </div>
    </div>

    {{-- Toast Container --}}
    <div id="toastAdminContainer" class="toast-admin-container"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            var toggle = document.getElementById('sidebarToggle');
            var close = document.getElementById('sidebarClose');
            function openSidebar() { sidebar.classList.add('show'); overlay.classList.add('show'); }
            function closeSidebar() { sidebar.classList.remove('show'); overlay.classList.remove('show'); }
            if (toggle) {
                toggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('show')) closeSidebar(); else openSidebar();
                });
            }
            if (overlay) overlay.addEventListener('click', closeSidebar);
            if (close) close.addEventListener('click', closeSidebar);

            setTimeout(function() {
                document.querySelectorAll('.alert-admin').forEach(function(el) {
                    el.style.transition = 'opacity 0.4s';
                    el.style.opacity = '0';
                    setTimeout(function() { el.style.display = 'none'; }, 400);
                });
            }, 5000);
        });
    </script>
    @stack('scripts')
</body>
</html>
