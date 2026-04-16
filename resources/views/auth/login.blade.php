<!DOCTYPE html>
{{-- resources/views/auth/login.blade.php --}}
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIAKAD</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

        :root {
            /* ✅ BLUE SOFT / NEON CLEAN */
            --primary: #2563eb;              /* blue-600 */
            --primary-glow: rgba(37,99,235,.35);

            --neon-cyan: #38bdf8;             /* sky-400 */
            --neon-purple: #1e40af;           /* blue-800 (bukan ungu lagi) */

            --dark: #0b1020;
            --dark2: #0f172a;
            --border-dark: rgba(255,255,255,.08);

            --font: 'Plus Jakarta Sans', sans-serif;

            /* LEFT PANEL */
            --left-bg: #f8fbff;
            --left-text: #0f172a;
            --left-muted: #64748b;
        }

        html, body { height:100%; }
        body { font-family:var(--font); background:var(--dark); display:flex; min-height:100vh; overflow:hidden; }


        .left-panel {
            flex:1;
            position:relative;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            padding:48px;
            background:var(--left-bg);
            overflow:hidden;
        }

        /* ✅ GRID TIPIS BERGERAK PELAN */

        .left-panel::before {
            content:'';
            position:absolute;
            inset:0;
            background-image:
                linear-gradient(rgba(37,99,235,.12) 1px, transparent 1px),
                linear-gradient(90deg, rgba(37,99,235,.12) 1px, transparent 1px);
            background-size:40px 40px;
            animation: gridMove 30s linear infinite;
            pointer-events:none;
            z-index:0;
        }

        /* ✅ SOFT WHITE–GREEN FLOW */

        .left-panel::after {
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(
                120deg,
                #ffffff,
                #ecfdf5,
                #ffffff
            );
            background-size:200% 200%;
            animation: softFlow 18s ease-in-out infinite;
            pointer-events:none;
            z-index:1;
        }

        /* ✅ SEMUA KONTEN DI ATAS BACKGROUND */
        .left-panel > * {
            position:relative;
            z-index:2;
        }

        .orb {
            position:absolute;
            border-radius:50%;
            filter:blur(70px);
            pointer-events:none;
            z-index:1;
        }

        .orb-1 {background:radial-gradient(circle,rgba(37,99,235,.18),transparent 70%);}
        .orb-2 {background:radial-gradient(circle,rgba(56,189,248,.14),transparent 70%);}
        .orb-3 {background:radial-gradient(circle,rgba(96,165,250,.12),transparent 70%);}

        .left-logo { position:relative; z-index:2; display:flex; align-items:center; gap:14px; }

        .left-logo .logo-box {background:linear-gradient(135deg,#2563eb,#1e40af);box-shadow:0 4px 20px rgba(37,99,235,.35);}

        .left-logo .logo-text h1 { font-size:16px; font-weight:800; letter-spacing:.5px; color:var(--left-text); }
        .left-logo .logo-text span { font-size:11px; color:var(--left-muted); letter-spacing:1px; }
        .left-hero { position:relative; z-index:2; }
        .left-hero .tag { display:inline-flex; align-items:center; gap:6px; padding:5px 14px; background:rgba(99,102,241,.08); border:1px solid rgba(99,102,241,.2); border-radius:20px; font-size:11px; font-weight:700; color:var(--primary); letter-spacing:1.5px; text-transform:uppercase; margin-bottom:20px; }
        .left-hero h2 { font-size:clamp(28px,3vw,42px); font-weight:900; line-height:1.15; color:var(--left-text); margin-bottom:16px; }
        .left-hero h2 span { background:linear-gradient(135deg,var(--primary),var(--neon-cyan)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .left-hero p { font-size:14px; color:var(--left-muted); line-height:1.75; max-width:380px; }
        .left-stats { position:relative; z-index:2; display:flex; gap:32px; }
        .stat-item .num { font-size:28px; font-weight:900; color:var(--left-text); line-height:1; }
        .stat-item .label { font-size:11px; color:var(--left-muted); margin-top:4px; letter-spacing:.5px; }
        .stat-divider { width:1px; background:rgba(99,102,241,.15); }


        /* RIGHT PANEL */
        .right-panel {
            width:440px;
            min-width:440px;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:48px 40px;
            background:var(--dark2);
            border-left:1px solid var(--border-dark);
            position:relative;
            overflow:hidden;
        }

        /* ✅ Neon garis atas bergerak */

        .right-panel::before {
            content:'';
            position:absolute;
            top:0;
            left:0;
            right:0;
            height:2px;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(52,211,153,.6),   /* emerald-400 */
                rgba(16,185,129,.9),   /* emerald-500 (utama) */
                rgba(52,211,153,.6),
                transparent
            );
            background-size:200% 100%;
            animation: neonSlide 7s linear infinite;
        }


        /* ✅ Orb neon biru pelan */
        .right-panel::after {
            content:'';
            position:absolute;
            width:320px;
            height:320px;
            background:radial-gradient(
                circle,
                rgba(56,189,248,.14),
                transparent 70%
            );
            bottom:-90px;
            right:-90px;
            border-radius:50%;
            filter:blur(65px);
            pointer-events:none;
            animation: floatSlow 10s ease-in-out infinite;
        }

        /* ✅ Orb neon kecil kiri atas */
        .right-neon-orb {
            position:absolute;
            width:220px;
            height:220px;
            background:radial-gradient(
                circle,
                rgba(37,99,235,.12),
                transparent 70%
            );
            top:-70px;
            left:-70px;
            border-radius:50%;
            filter:blur(55px);
            pointer-events:none;
            animation: floatSlow 12s ease-in-out infinite reverse;
        }

        /* ✅ Form tetap clean, no over-animation */
        .form-wrap {
            width:100%;
            position:relative;
            z-index:2;
            animation:fadeUp .5s ease;
        }
        @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        @keyframes lineMove { from { background-position: 0 0; } to   { background-position: 80px 80px; } }
        @keyframes gridMove {from { background-position: 0 0; }to   { background-position: 80px 80px; }}
        @keyframes softFlow {0% { background-position: 0% 50%; }100% { background-position: 100% 50%; }}

        .form-title { margin-bottom:28px; }
        .form-title h3 { font-size:26px; font-weight:800; color:#fff; margin-bottom:6px; }
        .form-title p { font-size:13px; color:rgba(255,255,255,.4); }
        .form-group { margin-bottom:16px; }
        .form-label { display:block; font-size:11px; font-weight:700; color:rgba(255,255,255,.45); letter-spacing:1.2px; text-transform:uppercase; margin-bottom:8px; }
        .input-wrap { position:relative; }
        .input-wrap i.icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); font-size:16px; color:rgba(255,255,255,.2); pointer-events:none; transition:color .2s; }

        .form-input {
            width:100%;
            padding:12px 14px 12px 44px;
            background: rgba(255,255,255,.04);
            border:1px solid rgba(255,255,255,.14);
            border-radius:10px;
            color:#ffffff;
            font-size:14px;
            font-family:var(--font);
            outline:none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .form-input::placeholder {
            color: rgba(255,255,255,.28);
        }

        /* ✅ FOCUS BERSIH & ELEGAN */
        .form-input:focus {
            background: rgba(255,255,255,.06);
            border-color:#34d399; /* emerald-400 */
            box-shadow: 0 0 0 2px rgba(52,211,153,.25);
        }


        .input-wrap i.icon {
            color: rgba(255,255,255,.25);
            transition: color .2s ease;
        }

        .input-wrap:focus-within i.icon {
            color:#34d399;
        }

        .toggle-pw { position:absolute; right:14px; top:50%; transform:translateY(-50%); background:none; border:none; color:rgba(255,255,255,.25); cursor:pointer; font-size:16px; transition:color .2s; padding:0; }
        .toggle-pw:hover { color:rgba(255,255,255,.6); }
        .form-footer-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
        .remember-wrap { display:flex; align-items:center; gap:8px; cursor:pointer; }
        .remember-wrap input[type="checkbox"] { width:16px; height:16px; accent-color:var(--primary); cursor:pointer; }
        .remember-wrap span { font-size:13px; color:rgba(255,255,255,.4); }
        .forgot-link { font-size:13px; color:#a5b4fc; text-decoration:none; font-weight:600; transition:color .2s; }
        .forgot-link:hover { color:#fff; }

        /* CAPTCHA BOX */
        .captcha-box {
            display:flex; align-items:center; gap:12px;
            background:rgba(255,255,255,.04);
            border:1px solid rgba(255,255,255,.1);
            border-radius:10px; padding:12px 14px;
            margin-bottom:16px;
        }
        .captcha-soal {
            font-size:18px; font-weight:800; color:#fff;
            background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.3);
            padding:6px 16px; border-radius:8px; letter-spacing:2px;
            white-space:nowrap; flex-shrink:0;
        }
        .captcha-equals { font-size:20px; color:rgba(255,255,255,.3); flex-shrink:0; }


        .captcha-input {
            flex:1;
            padding:10px 12px;
            background: rgba(255,255,255,.06);
            border:1px solid rgba(255,255,255,.18);
            border-radius:8px;

            color:#ffffff;
            font-size:16px;
            font-weight:700;
            font-family:var(--font);
            text-align:center;

            outline:none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .captcha-input::placeholder {
            color: rgba(255,255,255,.3);
        }

        /* ✅ FOCUS BERSIH, SAMA DENGAN INPUT LOGIN */
        .captcha-input:focus {
            background: rgba(255,255,255,.08);
            border-color:#34d399;               /* emerald-400 */
            box-shadow:0 0 0 2px rgba(52,211,153,.25);
        }

        .captcha-label { font-size:10px; color:rgba(255,255,255,.3); letter-spacing:1px; text-transform:uppercase; margin-bottom:6px; }


        .btn-login {
            width:100%;
            padding:14px;
            background: linear-gradient(
                120deg,
                #10b981, /* emerald-500 */
                #34d399, /* emerald-400 */
                #10b981
            );
            background-size:200% 200%;
            animation: buttonGreenFlow 7s linear infinite;

            border:none;
            border-radius:10px;
            color:#ffffff;
            font-size:15px;
            font-weight:700;
            font-family:var(--font);
            cursor:pointer;

            display:flex;
            align-items:center;
            justify-content:center;
            gap:8px;

            box-shadow:0 4px 18px rgba(16,185,129,.35);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .btn-login:hover {
            transform:translateY(-1px);
            box-shadow:
                0 8px 26px rgba(16,185,129,.45),
                0 0 12px rgba(52,211,153,.35);
        }


.btn-login:active {
    transform:translateY(0);
}

.btn-login.loading {
    pointer-events:none;
    opacity:.8;
}

        .form-divider { display:flex; align-items:center; gap:12px; margin:20px 0; }
        .form-divider::before, .form-divider::after { content:''; flex:1; height:1px; background:rgba(255,255,255,.08); }
        .form-divider span { font-size:11px; color:rgba(255,255,255,.3); letter-spacing:1px; }
        .alert-error { padding:10px 14px; background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25); border-radius:10px; font-size:13px; color:#fca5a5; margin-bottom:16px; display:flex; gap:8px; align-items:flex-start; }
        .alert-warning { padding:10px 14px; background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.25); border-radius:10px; font-size:13px; color:#fcd34d; margin-bottom:16px; display:flex; gap:8px; align-items:flex-start; }

        /* Attempts indicator */
        .attempts-bar { display:flex; gap:4px; margin-top:8px; }
        .attempt-dot { width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,.15); }
        .attempt-dot.used { background:#ef4444; }

        @media (max-width:768px) {
            body { overflow:auto; flex-direction:column; background:#09090f; }
            .left-panel { display:none; }
            .right-panel { width:100%; min-width:unset; min-height:100vh; border-left:none; padding:40px 28px; background:#09090f; }
            .mobile-logo { display:flex !important; }
        }
        @media (max-width:480px) { .right-panel { padding:36px 20px; } }
        .mobile-logo { display:none; align-items:center; gap:12px; margin-bottom:32px; }
        .mobile-logo .logo-box { width:40px; height:40px; background:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:900; color:#fff; }
        .mobile-logo .logo-text h1 { font-size:15px; font-weight:800; color:#fff; }
        .mobile-logo .logo-text span { font-size:11px; color:rgba(255,255,255,.4); }
        @keyframes spin { to { transform:rotate(360deg); } }

        @keyframes neonSlide {
            0%   { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        @keyframes neonPulse {
            0%,100% {
                box-shadow: 0 0 12px rgba(56,189,248,.35);
            }
            50% {
                box-shadow: 0 0 22px rgba(56,189,248,.55);
            }
        }

        @keyframes floatSlow {
            0%   { transform: translate(0,0); }
            50%  { transform: translate(-15px,-20px); }
            100% { transform: translate(0,0); }
        }

        @keyframes buttonGreenFlow {
            0%   { background-position:   0% 50%; }
            100% { background-position: 200% 50%; }
        }

    </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="left-logo">
        <div class="logo-box" style="background:transparent;padding:2px;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                style="width:34px;height:34px;object-fit:contain;"
                onerror="this.style.display='none';this.parentElement.style.background='#6366f1';this.parentElement.innerHTML='S';">
        </div>
        <div class="logo-text">
            <h1>SIAKAD V2</h1>
            <span>SISTEM AKADEMIK</span>
        </div>
    </div>
    <div class="left-hero">
        <div class="tag"><i class="bi bi-stars"></i> SMA Negeri 1 Kuta Selatan</div>
        <h2>Sistem Informasi<br><span>Akademik</span> Terpadu</h2>
        <p>Platform pengelolaan akademik yang terintegrasi — absensi, jurnal, prestasi, dan laporan dalam satu sistem.</p>
    </div>
    <div class="left-stats">
        <div class="stat-item">
            <div class="num">{{ \App\Models\Siswa::where('is_archived',false)->count() }}</div>
            <div class="label">Siswa Aktif</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="num">{{ \App\Models\Guru::count() }}</div>
            <div class="label">Guru & GTK</div>
        </div>

        <div class="stat-item">
            <div class="num">
                {{ \App\Models\Rombel::where('jenis_rombel', 1)
                    ->where('is_archived', false)
                    ->count() }}
            </div>
            <div class="label">Rombongan Belajar</div>
        </div>

    </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
    <div class="right-neon-orb"></div>
    <div class="form-wrap">
        <div class="mobile-logo">
            <div class="logo-box">S</div>
            <div class="logo-text"><h1>SIAKAD V2</h1><span>SISTEM AKADEMIK</span></div>
        </div>

        <div class="form-title">
            <h3>Selamat Datang 👋</h3>
            <p>Masuk ke akun Anda untuk melanjutkan</p>
        </div>

        {{-- Error / Lockout --}}
        @if($errors->has('login'))
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
            <span>{{ $errors->first('login') }}</span>
        </div>
        @endif

        @if($errors->has('captcha'))
        <div class="alert-warning">
            <i class="bi bi-shield-exclamation" style="flex-shrink:0;margin-top:1px;"></i>
            <span>{{ $errors->first('captcha') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf

            <div class="form-group">
                <label class="form-label">Username</label>
                <div class="input-wrap">
                    <i class="bi bi-person icon"></i>
                    <input type="text" name="login" class="form-input"
                        placeholder="Username atau email"
                        value="{{ old('login') }}"
                        autocomplete="username" autofocus required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock icon"></i>
                    <input type="password" name="password" class="form-input"
                        id="pw-input" placeholder="Masukkan password"
                        autocomplete="current-password" required>
                    <button type="button" class="toggle-pw" onclick="togglePw()">
                        <i class="bi bi-eye" id="pw-icon"></i>
                    </button>
                </div>
            </div>

            {{-- CAPTCHA --}}
            <div>
                <p class="captcha-label">Verifikasi Keamanan</p>
                <div class="captcha-box">
                    <div class="captcha-soal">{{ $a }} + {{ $b }} = ?</div>
                    <div class="captcha-equals"></div>
                    <input type="number" name="captcha" class="captcha-input"
                        placeholder="Jawab" min="0" max="18"
                        autocomplete="off" required>
                </div>
            </div>

            <div class="form-footer-row">
                <label class="remember-wrap">
                    <input type="checkbox" name="remember">
                    <span>Ingat saya</span>
                </label>
                <a href="{{ route('password.reset.form') }}" class="forgot-link">Lupa password?</a>
            </div>

            <button type="submit" class="btn-login" id="btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                Masuk ke Sistem
            </button>
        </form>

        {{-- Google Oauth --}}
        {{-- Divider --}}
            <div style="display:flex;align-items:center;gap:12px;margin:20px 0;">
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                <span style="font-size:12px;color:#94a3b8;white-space:nowrap;">atau masuk dengan</span>
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            </div>

            {{-- Tombol Login Google --}}
            <a href="{{ route('auth.google') }}"
                style="
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    gap:10px;
                    width:100%;
                    padding:10px 16px;
                    border:1.5px solid #e2e8f0;
                    border-radius:10px;
                    background:#fff;
                    color:#374151;
                    font-size:14px;
                    font-weight:600;
                    text-decoration:none;
                    transition:background .15s, border-color .15s, box-shadow .15s;
                    cursor:pointer;
                "
                onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1';this.style.boxShadow='0 2px 8px rgba(0,0,0,.08)'"
                onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0';this.style.boxShadow='none'"
            >
                {{-- Logo Google SVG --}}
                <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Masuk dengan Google
            </a>

            {{-- Catatan kecil --}}
            <p style="text-align:center;font-size:11px;color:#94a3b8;margin-top:10px;">
                Hanya akun yang terdaftar di sistem yang dapat masuk
            </p>
            {{-- BATAS GOOGLE OAUTH --}}

        <div class="form-divider"><span>INFO</span></div>
        <p style="font-size:12px;color:rgba(206,198,198,.8);text-align:center;line-height:1.7;">
            Hubungi administrator jika Anda belum memiliki akun atau mengalami kendala login.
        </p>
        <br>
        <br>
        <footer>
            <p style="font-size:12px;color:rgba(206,198,198,.8);text-align:center;line-height:1.7;">
                &copy; {{ date('Y') }}
                <br>
                <b>SMA Negeri 1 Kuta Selatan.</b>
                <br>
                Support By Teknosaka Team
        </p>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function togglePw() {
    const input = document.getElementById('pw-input');
    const icon  = document.getElementById('pw-icon');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

document.getElementById('login-form').addEventListener('submit', function() {
    const btn = document.getElementById('btn-login');
    btn.classList.add('loading');
    btn.innerHTML = '<span style="width:16px;height:16px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;display:inline-block;animation:spin .6s linear infinite;"></span> Memproses...';
});

// Hanya angka di captcha
document.querySelector('[name="captcha"]').addEventListener('keypress', function(e) {
    if (!/[0-9]/.test(e.key)) e.preventDefault();
});

@if(session('status'))
    Swal.fire({ icon:'success', title:'Berhasil!', text:'{{ session("status") }}', timer:3000, showConfirmButton:false, background:'#0d0d0d', color:'#fff' });
@endif
</script>
</body>
</html>
