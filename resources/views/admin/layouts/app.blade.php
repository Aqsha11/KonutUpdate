<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ $site_settings['site_name'] ?? 'Konut.Update' }}</title>
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
    @if(!empty($site_settings['favicon']))
        <link rel="icon" type="image/png" href="{{ Storage::url($site_settings['favicon']) }}">
    @endif
    @stack('styles')
</head>
<body>
    <div id="landscapeBanner" class="landscape-banner" hidden>
        <div class="landscape-banner-content">
            <span class="material-symbols-outlined landscape-banner-icon">screen_rotation</span>
            <span>Gunakan mode landscape untuk pengalaman admin yang lebih baik</span>
        </div>
        <button type="button" class="landscape-banner-close" id="landscapeBannerClose" aria-label="Tutup">
            <i class="bi bi-x"></i>
        </button>
    </div>
    <div class="wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <nav class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                @if(!empty($site_settings['logo']))
                    <img src="{{ Storage::url($site_settings['logo']) }}" alt="{{ $site_settings['site_name'] ?? 'Konut.Update' }}">
                @else
                    <div class="brand-text"><span>K</span>onut.Update</div>
                @endif
                <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Tutup sidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="sidebar-nav">
                <div class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </div>
                <div class="nav-label">Konten</div>
                <div class="nav-item">
                    <a href="{{ route('admin.posts.index') }}" class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                        <i class="bi bi-newspaper"></i> Berita
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Kategori
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <i class="bi bi-file-text"></i> Halaman
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.ads.index') }}" class="nav-link {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}">
                        <i class="bi bi-megaphone"></i> Iklan
                    </a>
                </div>
                @if(auth()->user()->role && auth()->user()->role->slug === 'super_admin')
                <div class="nav-divider"></div>
                <div class="nav-label">Administrasi</div>
                <div class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Users
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock"></i> Role
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <i class="bi bi-key"></i> Permission
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </div>
                @endif
            </div>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="user-details">
                        <a href="{{ route('admin.profile.index') }}" class="user-name">{{ auth()->user()->name }}</a>
                        <div class="user-role">{{ auth()->user()->role ? ucfirst(str_replace('_', ' ', auth()->user()->role->name)) : 'Unknown' }}</div>
                    </div>
                </div>
                <a href="{{ route('logout') }}" class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
            </div>
        </nav>

        <div class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="topbar-search">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="globalSearch" placeholder="Cari menu..." autocomplete="off">
                    </div>
                </div>
                <div class="topbar-right">
                    <button class="topbar-btn" id="darkModeToggle" title="Dark Mode">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                    <div class="dropdown">
                        <button class="topbar-btn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="badge-dot"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-admin" style="min-width: 280px;">
                            <li><h6 class="dropdown-header">Notifikasi</h6></li>
                            <li><span class="dropdown-item-text text-muted" style="font-size:0.85rem;">Tidak ada notifikasi</span></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="topbar-user" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <span class="user-name-sm">{{ auth()->user()->name }} <i class="bi bi-chevron-down"></i></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-admin">
                            <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i class="bi bi-person"></i> Profil</a></li>
                            <li><a class="dropdown-item" href="{{ url('/') }}" target="_blank"><i class="bi bi-globe"></i> Lihat Website</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-left"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
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
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div id="toastAdminContainer" class="toast-admin-container"></div>

    {{-- Confirmation Modal --}}
    <div class="modal fade modal-admin-confirm" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center pb-0">
                    <div id="confirmIcon" class="confirm-icon warning">
                        <i class="bi bi-question-circle"></i>
                    </div>
                </div>
                <div class="modal-body text-center">
                    <h5 class="confirm-title" id="confirmTitle">Konfirmasi</h5>
                    <p class="confirm-message" id="confirmMessage">Apakah Anda yakin?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn-admin btn-admin-secondary" id="confirmCancelBtn" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn-admin btn-admin-primary" id="confirmBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dark Mode
        (function() {
            var html = document.documentElement;
            var saved = localStorage.getItem('adminTheme');
            var isDark = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) html.classList.add('dark');
            var toggle = document.getElementById('darkModeToggle');
            var icon = toggle && toggle.querySelector('i');
            if (icon) icon.className = isDark ? 'bi bi-sun' : 'bi bi-moon-stars';
            if (toggle) {
                toggle.addEventListener('click', function() {
                    html.classList.toggle('dark');
                    var nowDark = html.classList.contains('dark');
                    localStorage.setItem('adminTheme', nowDark ? 'dark' : 'light');
                    if (icon) icon.className = nowDark ? 'bi bi-sun' : 'bi bi-moon-stars';
                });
            }
        })();

        (function() {
            var banner = document.getElementById('landscapeBanner');
            var closeBtn = document.getElementById('landscapeBannerClose');
            if (banner && !sessionStorage.getItem('landscapeBannerDismissed')) {
                function checkOrientation() {
                    if (window.innerWidth <= 768 && window.innerHeight > window.innerWidth) {
                        banner.removeAttribute('hidden');
                    } else {
                        banner.setAttribute('hidden', '');
                    }
                }
                window.addEventListener('resize', checkOrientation);
                window.addEventListener('orientationchange', function() {
                    setTimeout(checkOrientation, 300);
                });
                checkOrientation();
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        banner.setAttribute('hidden', '');
                        sessionStorage.setItem('landscapeBannerDismissed', 'true');
                    });
                }
            }
        })();

        document.addEventListener('DOMContentLoaded', function() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            var toggle = document.getElementById('sidebarToggle');
            var close = document.getElementById('sidebarClose');
            function openSidebar() { sidebar.classList.add('show'); overlay.classList.add('show'); }
            function closeSidebar() { sidebar.classList.remove('show'); overlay.classList.remove('show'); }
            if (toggle) {
                toggle.addEventListener('click', function() {
                    if (window.innerWidth <= 991) {
                        if (sidebar.classList.contains('show')) closeSidebar(); else openSidebar();
                    }
                });
            }
            if (overlay) overlay.addEventListener('click', closeSidebar);
            if (close) close.addEventListener('click', closeSidebar);

            var search = document.getElementById('globalSearch');
            if (search) {
                search.addEventListener('keyup', function() {
                    var val = this.value.toLowerCase();
                    document.querySelectorAll('.sidebar-nav .nav-item').forEach(function(item) {
                        item.style.display = item.textContent.toLowerCase().indexOf(val) > -1 ? '' : 'none';
                    });
                });
            }

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
