<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIAKAD - {{ config('app.name') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


    {{--    --primary:     #6366f1;
            --primary-dark:#4f46e5;
            --secondary:   #f59e0b;
            --success:     #10b981;
            --danger:      #ef4444;
            --sidebar-bg:  #1e1b4b; --}}
    <!-- Custom CSS -->

    <style>
        :root {
            --primary:     #6366f1;
            --primary-dark:#000000;
            --secondary:   #f59e0b;
            --success:     #10b981;
            --danger:      #ef4444;
            --sidebar-bg:  #000000;
            --sidebar-w:   260px;
            --header-h:    64px;
            --font:        'Plus Jakarta Sans', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font);
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform .3s ease;
            overflow-y: auto;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand .logo {
            width: 38px; height: 38px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff; font-weight: 800;
        }

        .sidebar-brand .brand-text h1 {
            font-size: 15px; font-weight: 800;
            color: #fff; letter-spacing: .5px;
        }

        .sidebar-brand .brand-text span {
            font-size: 11px; color: rgba(255,255,255,.45);
            letter-spacing: .5px;
        }

        .sidebar-user {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-user .avatar {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 700; color: #fff;
            float: left; margin-right: 10px;
        }

        .sidebar-user .user-info h4 {
            font-size: 13px; font-weight: 600; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .sidebar-user .user-info span {
            font-size: 11px; color: rgba(255,255,255,.45);
            text-transform: capitalize;
        }

        .sidebar-user::after { content: ''; display: table; clear: both; }

        .sidebar-nav { padding: 16px 0; flex: 1; }

        .nav-label {
            font-size: 10px; font-weight: 700;
            color: rgba(255,255,255,.3);
            letter-spacing: 1.5px; text-transform: uppercase;
            padding: 8px 24px 4px;
        }

        .nav-item a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 24px;
            color: rgba(255,255,255,.55);
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .2s;
        }

        .nav-item a:hover,
        .nav-item a.active {
            color: #fff;
            background: rgba(255,255,255,.07);
            border-left-color: var(--primary);
        }

        .nav-item a i { font-size: 17px; width: 20px; text-align: center; }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .btn-logout {
            display: flex; align-items: center; gap: 10px;
            width: 100%; padding: 10px 16px;
            background: rgba(239,68,68,.15);
            color: #fca5a5;
            border: 1px solid rgba(239,68,68,.25);
            border-radius: 8px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: all .2s;
        }

        .btn-logout:hover {
            background: rgba(239,68,68,.3);
            color: #fff;
        }

        /* ===== HEADER ===== */
        .header {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--header-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            z-index: 99;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }

        .header-left h2 {
            font-size: 17px; font-weight: 700; color: #0f172a;
        }

        .header-left p {
            font-size: 12px; color: #94a3b8;
        }

        .header-right {
            display: flex; align-items: center; gap: 16px;
        }

        .header-btn {
            width: 38px; height: 38px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: #64748b;
            font-size: 16px; position: relative;
            text-decoration: none;
            transition: all .2s;
        }

        .header-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        .notif-badge {
            position: absolute; top: -4px; right: -4px;
            width: 16px; height: 16px;
            background: var(--danger);
            border-radius: 50%;
            font-size: 9px; color: #fff; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }

        .header-profile {
            position: relative;
        }

        .header-profile-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 6px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all .2s;
            border: none;
            outline: none;
        }
        .header-profile-btn:hover {
            background: #ede9fe;
            border-color: #6366f1;
        }

        .header-profile .avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }

        .header-profile .name {
            font-size: 13px; font-weight: 600; color: #0f172a;
            max-width: 140px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* Dropdown */
        .profile-dropdown {
            display: none;
            position: absolute; top: calc(100% + 8px); right: 0;
            min-width: 200px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,.10);
            z-index: 200;
            overflow: hidden;
        }
        .profile-dropdown.open { display: block; }

        .profile-dropdown-header {
            padding: 14px 16px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }
        .profile-dropdown-header .pd-name {
            font-size: 13px; font-weight: 700; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .profile-dropdown-header .pd-role {
            font-size: 11px; color: rgba(255,255,255,.7);
            text-transform: capitalize; margin-top: 2px;
        }

        .profile-dropdown-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px;
            font-size: 13px; font-weight: 500; color: #374151;
            text-decoration: none;
            transition: background .15s;
        }
        .profile-dropdown-item:hover { background: #f8fafc; color: #6366f1; }
        .profile-dropdown-item i { font-size: 15px; width: 18px; text-align: center; color: #94a3b8; }
        .profile-dropdown-item:hover i { color: #6366f1; }
        .profile-dropdown-divider { height: 1px; background: #f1f5f9; margin: 4px 0; }
        .profile-dropdown-item.danger { color: #ef4444; }
        .profile-dropdown-item.danger i { color: #ef4444; }
        .profile-dropdown-item.danger:hover { background: #fff1f2; }

        @media (max-width: 768px) {
            .header-profile .name,
            .header-profile .bi-chevron-down { display: none; }
            .header-profile-btn { padding: 4px; background: transparent; border: none; }

        }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            padding-top: var(--header-h);
            min-height: 100vh;
        }

        .main-content {
            padding: 28px;
            overflow-x: hidden;
            max-width: 100%;
            min-width: 0;
        }

        /* ===== FOOTER ===== */
        .footer {

            padding: 16px 28px;

            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: #94a3b8;
        }

        .footer strong { color: var(--primary); }

        /* ===== CARDS ===== */
        .card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }

        .card-header h3 {
            font-size: 15px; font-weight: 700; color: #0f172a;
        }

        .card-body { padding: 22px; }

        /* ===== STAT CARDS ===== */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 22px;
            border: 1px solid #e2e8f0;
            display: flex; align-items: center; gap: 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }

        .stat-icon {
            width: 54px; height: 54px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #fff; flex-shrink: 0;
        }

        .stat-info h3 { font-size: 22px; font-weight: 800; color: #0f172a; }
        .stat-info p  { font-size: 13px; color: #64748b; font-weight: 500; }

        /* ===== TABLES ===== */
        .table-wrapper { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }

        table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .8px;
            color: #94a3b8;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        table tbody td {
            padding: 14px 16px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid #f1f5f9;
        }

        table tbody tr:hover { background: #f8fafc; }

        /* ===== BADGES ===== */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px; font-weight: 600;
        }

        .badge-primary { background: #ede9fe; color: var(--primary); }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; border: none;
            text-decoration: none; transition: all .2s;
        }

        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); color: #fff; }
        .btn-success { background: var(--success); color: #fff; }
        .btn-danger  { background: var(--danger);  color: #fff; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        /* ===== GRID ===== */
        .grid { display: grid; gap: 20px; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }

        @media (max-width: 1024px) { .grid-4 { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 768px)  { .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; } }

        /* Page title */
        .page-title {
            margin-bottom: 24px;
        }
        .page-title h1 {
            font-size: 22px; font-weight: 800; color: #0f172a;
        }
        .page-title p {
            font-size: 13px; color: #94a3b8; margin-top: 2px;
        }

        /* Hamburger mobile */
        .hamburger {
            display: none;
            background: none; border: none;
            font-size: 22px; cursor: pointer; color: #374151;
        }

        @media (max-width: 768px) {
            .hamburger { display: block; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .header { left: 0; }
            .main-wrapper { margin-left: 0; }
            .footer { margin-left: 0; }
            .main-content { padding: 16px; overflow-x: hidden; }
            body { overflow-x: hidden; }
            img, video, iframe, table { max-width: 100%; }
        }

        /* Overlay mobile */
        .overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 99;
        }
        .overlay.show { display: block; }
/* Override Pagination
        nav[aria-label="Pagination Navigation"] {
            display: flex;
            align-items: center;
            gap: 4px;
        }*/
        nav[aria-label="Pagination Navigation"] svg {
            width: 14px !important;
            height: 14px !important;
        }
        nav[aria-label="Pagination Navigation"] span,
        nav[aria-label="Pagination Navigation"] a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            color: #374151;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all .2s;
        }
        nav[aria-label="Pagination Navigation"] a:hover {
            background: #ede9fe;
            color: #6366f1;
            border-color: #6366f1;
        }
        nav[aria-label="Pagination Navigation"] span[aria-current="page"] span {
            background: #6366f1;
            color: #fff;
            border-color: #6366f1;
        }
        nav[aria-label="Pagination Navigation"] span[aria-disabled="true"] span {
            color: #cbd5e1;
            cursor: not-allowed;
        }

    </style>

    @stack('styles')
</head>
<body>

<!-- Overlay Mobile -->
<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo">S</div>
        <div class="brand-text">
            <h1>SIAKAD V2</h1>
            <span>Sistem Akademik</span>
        </div>
    </div>

    @auth
    <div class="sidebar-user">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <h4>{{ auth()->user()->name }}</h4>
            <span>{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</span>
        </div>
    </div>
    @endauth

    <nav class="sidebar-nav">
        @include('partials.sidebar')
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="button" class="btn-logout" onclick="confirmLogout(this.closest('form'))">
                <i class="bi bi-box-arrow-left"></i>
                Keluar
            </button>
        </form>
    </div>
</aside>

@php
    // Dashboard URL sesuai role — dipakai di header breadcrumb
    $_dashUrl = '/';
    if (auth()->check()) {
        $_role = auth()->user()->getRoleNames()->first();
        $_map  = [
            'admin'                => '/dashboard/admin',
            'kepala_sekolah'       => '/dashboard/kepala-sekolah',
            'wakil_kepala_sekolah' => '/dashboard/wakil-kepala',
            'guru'                 => '/dashboard/guru',
            'bk'                   => '/dashboard/bk',
            'tata_usaha'           => '/dashboard/tata-usaha',
            'siswa'                => '/dashboard/siswa',
        ];
        $_dashUrl = $_map[$_role] ?? '/';
    }
@endphp

<!-- HEADER -->
<header class="header">
    <div class="header-left" style="display:flex;align-items:center;gap:14px;">
        <button class="hamburger" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <div style="display:flex;align-items:center;gap:8px;">
            <a href="{{ $_dashUrl }}" style="display:flex;align-items:center;justify-content:center;width:34px;height:34px;background:#f1f5f9;border-radius:8px;color:#64748b;text-decoration:none;font-size:16px;transition:all .2s;"
               title="Dashboard" onmouseover="this.style.background='#6366f1';this.style.color='#fff'" onmouseout="this.style.background='#f1f5f9';this.style.color='#64748b'">
                <i class="bi bi-grid-1x2-fill"></i>
            </a>
            <div>
                <h2>@yield('page-title', 'Dashboard')</h2>
                <p>@yield('page-subtitle', date('l, d F Y'))</p>
            </div>
        </div>
    </div>
    <div class="header-right">
        <!--<a href="#" class="header-btn">
            <i class="bi bi-bell"></i>
            <span class="notif-badge">3</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="header-btn">
            <i class="bi bi-gear"></i>
        </a>-->
        @auth
        <div class="header-profile" id="profileMenu">
            <button class="header-profile-btn" onclick="toggleProfileMenu()" type="button">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <span class="name">{{ auth()->user()->name }}</span>
                <i class="bi bi-chevron-down" style="font-size:11px;color:#94a3b8;"></i>
            </button>
            <div class="profile-dropdown" id="profileDropdown">
                <div class="profile-dropdown-header">
                    <div class="pd-name">{{ auth()->user()->name }}</div>
                    <div class="pd-role">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</div>
                </div>
                <a href="{{ route('profile.edit') }}" class="profile-dropdown-item">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </a>
                <a href="{{ route('password.change') }}" class="profile-dropdown-item">
                    <i class="bi bi-key-fill"></i> Ganti Password
                </a>
                <div class="profile-dropdown-divider"></div>
                <a href="#" class="profile-dropdown-item danger" onclick="event.preventDefault(); confirmLogout(document.getElementById('logout-form'))">
                    <i class="bi bi-box-arrow-left"></i> Keluar
                </a>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
            </div>
        </div>
        @endauth
    </div>
</header>

<!-- MAIN -->
<div class="main-wrapper">
    <main class="main-content">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <span>&copy; {{ date('Y') }} <strong>SIAKAD</strong> — Sistem Informasi Akademik</span>
        <span>Made with ❤️ Teknosaka Team</span>
    </footer>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    // Toggle Sidebar Mobile
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('overlay').classList.toggle('show');
    }

    // Konfirmasi Logout
    function confirmLogout(form) {
        Swal.fire({
            title: 'Keluar?',
            text: 'Kamu yakin ingin keluar dari sistem?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }

    // Konfirmasi Delete
    function confirmDelete(form) {
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data yang dihapus tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }

    // SweetAlert untuk flash message
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', timer: 2500, showConfirmButton: false });
    @endif

    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}' });
    @endif

    @if(session('warning'))
        Swal.fire({ icon: 'warning', title: 'Perhatian!', text: '{{ session('warning') }}' });
    @endif
</script>

@stack('scripts')
<script>
function toggleProfileMenu() {
    document.getElementById('profileDropdown').classList.toggle('open');
}
// Tutup dropdown kalau klik di luar
document.addEventListener('click', function(e) {
    const menu = document.getElementById('profileMenu');
    if (menu && !menu.contains(e.target)) {
        document.getElementById('profileDropdown')?.classList.remove('open');
    }
});
</script>

@auth
<script>
(function() {
    const timeouts = {siswa:30,guru:60,bk:60,tata_usaha:60,admin:120,kepala_sekolah:120,wakil_kepala_sekolah:120};
    const menit = timeouts['{{ auth()->user()->getRoleNames()->first() }}'] || 60;
    let timer;
    function reset() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            Swal.fire({title:'⏳ Sesi Hampir Berakhir',text:'Klik Tetap Aktif untuk melanjutkan.',icon:'warning',
                confirmButtonText:'Tetap Aktif',showCancelButton:true,cancelButtonText:'Logout',
                timer:120000,timerProgressBar:true
            }).then(r => r.isConfirmed ? (fetch(location.href,{method:'HEAD'}), reset()) : document.getElementById('logout-form')?.submit());
        }, (menit - 2) * 60000);
    }
    ['click','keydown','mousemove','touchstart'].forEach(e => document.addEventListener(e, reset, {passive:true}));
    reset();
})();
</script>
@endauth
</body>
</html>
