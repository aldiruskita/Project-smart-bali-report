<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <title>@yield('title', 'Beranda') - Smart Bali Report (SBR)</title>
    <meta name="description" content="@yield('description', 'Lapor Cepat, Bali Hebat — Platform pelaporan masalah lingkungan berbasis web')">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- Mobile Drawer Overlay --}}
    <div class="mobile-drawer-overlay" id="drawerOverlay"></div>
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="mobile-drawer-header">
            <div class="drawer-logo">
                <img src="{{ asset('images/logo-icon.png') }}" alt="SBR">
                <span>SMART BALI REPORT</span>
            </div>
            <div class="drawer-tagline">Lapor Cepat, Bali Hebat</div>
        </div>
        <nav class="mobile-drawer-nav">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                <span class="material-symbols-rounded">home</span> Beranda
            </a>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.index') ? 'active' : '' }}">
                <span class="material-symbols-rounded">map</span> Peta Laporan
            </a>
            @auth
                @php $drawerUser = auth()->user(); @endphp
                @if($drawerUser->isWarga())
                    <a href="{{ route('reports.create') }}" class="{{ request()->routeIs('reports.create') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">campaign</span> Buat Laporan
                    </a>
                    <a href="{{ route('reports.my') }}" class="{{ request()->routeIs('reports.my') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">history</span> Laporan Saya
                    </a>
                @endif
                @if($drawerUser->isPetugas())
                    <a href="{{ route('officer.dashboard') }}" class="{{ request()->routeIs('officer.*','tasks.*') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">engineering</span> Tugas Saya
                    </a>
                    <a href="{{ route('reports.create') }}" class="{{ request()->routeIs('reports.create') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">campaign</span> Buat Laporan
                    </a>
                @endif
                @if($drawerUser->isAdmin())
                    <div class="drawer-divider"></div>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">dashboard</span> Dashboard Admin
                    </a>
                    <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">description</span> Kelola Laporan
                    </a>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">group</span> Kelola User
                    </a>
                @endif
                <div class="drawer-divider"></div>
                <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">person</span> Profil
                </a>
                <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">notifications</span> Notifikasi
                </a>
            @endauth
        </nav>
        <div class="mobile-drawer-footer">
            @auth
            <button type="button" onclick="showLogoutModal()" style="background:none;border:1px solid var(--glass-border);color:var(--text-muted);width:100%;padding:12px;border-radius:10px;cursor:pointer;font-family:inherit;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:8px;">
                <span class="material-symbols-rounded" style="font-size:18px;">logout</span> Keluar
            </button>
            @else
            <div style="display:flex;gap:8px;">
                <a href="{{ route('login') }}" class="btn-secondary btn-sm" style="flex:1;justify-content:center;">Masuk</a>
                <a href="{{ route('register') }}" class="btn-primary btn-sm" style="flex:1;justify-content:center;">Daftar</a>
            </div>
            @endauth
        </div>
    </div>

    {{-- Navbar --}}
    <nav class="navbar">
        <div class="navbar-inner">
            {{-- Mobile hamburger --}}
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <span class="material-symbols-rounded">menu</span>
            </button>

            <a href="{{ route('home') }}" class="nav-logo" style="display:flex;align-items:center;gap:10px;">
                <img src="{{ asset('images/logo-icon.png') }}" alt="SBR" style="height:36px;width:36px;object-fit:contain;">
                <div style="display:flex;flex-direction:column;line-height:1.15;">
                    <span style="font-family:'Noto Serif',serif;font-weight:700;font-size:14px;color:var(--primary);letter-spacing:0.02em;">SMART BALI REPORT</span>
                    <span style="font-size:9px;color:var(--text-muted);letter-spacing:0.15em;font-weight:600;">LAPOR CEPAT, BALI HEBAT</span>
                </div>
            </a>
            <div class="nav-links">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">Laporan</a>

                @auth
                    @php $authUser = auth()->user(); @endphp

                    {{-- === WARGA: Buat & Pantau Laporan === --}}
                    @if($authUser->isWarga())
                        <a href="{{ route('reports.create') }}" class="nav-link {{ request()->routeIs('reports.create') ? 'active' : '' }}">Buat Laporan</a>
                        <a href="{{ route('reports.my') }}" class="nav-link {{ request()->routeIs('reports.my') ? 'active' : '' }}">Laporan Saya</a>
                    @endif

                    {{-- === PETUGAS: Tugas + Laporan === --}}
                    @if($authUser->isPetugas())
                        @php $pendingTasks = \App\Models\Task::where('assigned_to', $authUser->id)->whereIn('status', ['assigned','accepted','in_progress'])->count(); @endphp
                        <a href="{{ route('officer.dashboard') }}" class="nav-link {{ request()->routeIs('officer.*','tasks.*') ? 'active' : '' }}">
                            Tugas Saya
                            @if($pendingTasks > 0)
                                <span style="background:var(--secondary);color:white;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:10px;margin-left:2px;">{{ $pendingTasks }}</span>
                            @endif
                        </a>
                        <a href="{{ route('reports.create') }}" class="nav-link {{ request()->routeIs('reports.create') ? 'active' : '' }}">Buat Laporan</a>
                        <a href="{{ route('reports.my') }}" class="nav-link {{ request()->routeIs('reports.my') ? 'active' : '' }}">Laporan Saya</a>
                    @endif

                    {{-- === ADMIN: Dashboard + Kelola + Tugas === --}}
                    @if($authUser->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                        <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                            Kelola
                            @php $pendingCount = \App\Models\Report::where('status','pending')->count(); @endphp
                            @if($pendingCount > 0)
                                <span style="background:var(--warning);color:#000;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:10px;margin-left:2px;">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('officer.dashboard') }}" class="nav-link {{ request()->routeIs('officer.*','tasks.*') ? 'active' : '' }}">Tugas</a>
                    @endif

                    {{-- === COMMON: Profile & Notif & Logout === --}}
                    <div style="display:flex;align-items:center;gap:4px;padding-left:12px;margin-left:8px;border-left:1px solid rgba(193,200,196,0.4);">
                        <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" style="display:inline-flex;align-items:center;gap:6px;padding:4px 8px;">
                            <div style="width:32px;height:32px;border-radius:50%;border:2px solid var(--accent);overflow:hidden;display:flex;align-items:center;justify-content:center;background:white;">
                                <img src="{{ $authUser->avatar ? asset('storage/'.$authUser->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($authUser->name).'&background=1A3A32&color=fff&size=40' }}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <div style="display:flex;flex-direction:column;">
                                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--primary);">{{ $authUser->name }}</span>
                                <span style="font-size:10px;color:var(--text-muted);font-family:'Noto Serif',serif;">{{ ucfirst($authUser->role) }}</span>
                            </div>
                        </a>
                        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}" style="padding:6px 8px;">
                            <span class="material-symbols-rounded" style="font-size:20px;">notifications</span>
                            @if($authUser->unreadNotifications->count() > 0)
                                <span style="background:var(--danger);color:white;border-radius:50%;width:16px;height:16px;display:inline-flex;align-items:center;justify-content:center;font-size:9px;">{{ $authUser->unreadNotifications->count() }}</span>
                            @endif
                        </a>
                        <button type="button" onclick="showLogoutModal()" class="nav-link" style="background:none;border:none;cursor:pointer;font-family:inherit;padding:6px 8px;">
                            <span class="material-symbols-rounded" style="font-size:20px;">logout</span>
                        </button>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary btn-sm">Daftar</a>
                @endauth
            </div>

            {{-- Mobile right side: avatar (auth) or login buttons (guest) --}}
            @auth
            <div class="mobile-menu-btn" style="background:none !important;" id="mobileProfileBtn">
                <div style="width:34px;height:34px;border-radius:50%;border:2px solid var(--accent);overflow:hidden;">
                    <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=1A3A32&color=fff&size=40' }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                </div>
            </div>
            @else
            <div style="display:flex;align-items:center;gap:8px;" class="mobile-auth-btns">
                <a href="{{ route('login') }}" class="btn-secondary btn-sm" style="padding:7px 14px;font-size:12px;border-radius:10px;">Masuk</a>
                <a href="{{ route('register') }}" class="btn-primary btn-sm" style="padding:7px 14px;font-size:12px;border-radius:10px;">Daftar</a>
            </div>
            @endauth
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="container" style="padding-bottom:0;">
            <div class="alert alert-success" style="display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:18px;">check_circle</span> {{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="container" style="padding-bottom:0;">
            <div class="alert alert-error" style="display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:18px;">error</span> {{ session('error') }}</div>
        </div>
    @endif

    {{-- Content --}}
    @yield('content')

    {{-- Footer --}}
    <footer class="site-footer">
        <div style="max-width:1280px;margin:0 auto;padding:48px 40px;display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:24px;">
            <div style="display:flex;align-items:center;gap:16px;">
                <img src="{{ asset('images/logo-icon.png') }}" alt="SBR" style="height:48px;width:48px;object-fit:contain;">
                <div>
                    <div style="font-size:1rem;font-weight:700;color:var(--primary);font-family:'Noto Serif',serif;letter-spacing:0.05em;margin-bottom:4px;">SMART BALI REPORT (SBR)</div>
                    <div style="font-family:'Noto Serif',serif;font-size:13px;color:var(--text-muted);">© {{ date('Y') }} Lapor Cepat, Bali Hebat.</div>
                </div>
            </div>
            <div style="display:flex;gap:24px;flex-wrap:wrap;">
                <a href="#" style="color:var(--text-muted);text-decoration:none;font-family:'Noto Serif',serif;font-size:14px;transition:color 0.2s;">Kebijakan Privasi</a>
                <a href="#" style="color:var(--text-muted);text-decoration:none;font-family:'Noto Serif',serif;font-size:14px;transition:color 0.2s;">Kontak Kami</a>
                <a href="#" style="color:var(--text-muted);text-decoration:none;font-family:'Noto Serif',serif;font-size:14px;transition:color 0.2s;">Bantuan</a>
            </div>
        </div>
    </footer>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @stack('scripts')

    {{-- Logout Confirmation Modal --}}
    <div id="logoutModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);" onclick="hideLogoutModal()"></div>
        <div style="position:relative;background:var(--bg-card);border:1px solid var(--glass-border);border-radius:24px;padding:36px;max-width:400px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,0.15);animation:modalPopIn 0.25s ease-out;">
            <div style="width:64px;height:64px;border-radius:50%;background:rgba(162,62,35,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <span class="material-symbols-rounded" style="font-size:32px;color:var(--secondary);">logout</span>
            </div>
            <h3 style="font-family:'Noto Serif',serif;font-size:1.2rem;font-weight:700;color:var(--primary);margin-bottom:8px;">Keluar dari Akun?</h3>
            <p style="color:var(--text-muted);font-size:14px;margin-bottom:28px;line-height:1.5;">Apakah Anda yakin ingin keluar dari Smart Bali Report?</p>
            <div style="display:flex;gap:12px;">
                <button type="button" onclick="hideLogoutModal()" class="btn-secondary" style="flex:1;justify-content:center;padding:12px;border-radius:14px;">Batal</button>
                <form action="{{ route('logout') }}" method="POST" style="flex:1;">
                    @csrf
                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:12px;border-radius:14px;gap:6px;background:var(--secondary);border-color:var(--secondary);">
                        <span class="material-symbols-rounded" style="font-size:18px;">logout</span> Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Image Lightbox --}}
    <div id="lightbox" style="display:none;position:fixed;inset:0;z-index:9998;align-items:center;justify-content:center;cursor:zoom-out;" onclick="closeLightbox()">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.85);backdrop-filter:blur(8px);"></div>
        <img id="lightboxImg" src="" alt="" style="position:relative;max-width:90vw;max-height:90vh;object-fit:contain;border-radius:12px;box-shadow:0 16px 60px rgba(0,0,0,0.5);animation:modalPopIn 0.25s ease-out;">
        <button onclick="closeLightbox()" style="position:absolute;top:20px;right:20px;background:rgba(255,255,255,0.1);border:none;color:white;width:44px;height:44px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(8px);z-index:1;">
            <span class="material-symbols-rounded" style="font-size:24px;">close</span>
        </button>
    </div>

    {{-- Mobile drawer + Lightbox + Logout modal script --}}
    <script>
    function showLogoutModal() {
        var m = document.getElementById('logoutModal');
        m.style.display = 'flex';
        // Close mobile drawer if open
        var drawer = document.getElementById('mobileDrawer');
        var overlay = document.getElementById('drawerOverlay');
        if (drawer) drawer.classList.remove('open');
        if (overlay) overlay.classList.remove('open');
    }
    function hideLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }
    // Lightbox
    function openLightbox(src) {
        var lb = document.getElementById('lightbox');
        document.getElementById('lightboxImg').src = src;
        lb.style.display = 'flex';
    }
    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
    }
    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { hideLogoutModal(); closeLightbox(); }
    });

    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('mobileMenuBtn');
        var drawer = document.getElementById('mobileDrawer');
        var overlay = document.getElementById('drawerOverlay');
        if (btn && drawer && overlay) {
            btn.addEventListener('click', function() {
                drawer.classList.add('open');
                overlay.classList.add('open');
            });
            overlay.addEventListener('click', function() {
                drawer.classList.remove('open');
                overlay.classList.remove('open');
            });
        }
        var profileBtn = document.getElementById('mobileProfileBtn');
        if (profileBtn) {
            profileBtn.addEventListener('click', function() {
                window.location.href = '{{ route("profile") }}';
            });
        }
    });
    </script>
</body>
</html>
