<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — SIAKAD</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --primary: #080cf0;
            --primary-glow: rgba(99,102,241,.2);
            --font: 'Plus Jakarta Sans', sans-serif;
        }
        html, body { height: 100%; }
        body {
            font-family: var(--font);
            background: #f1f5f9;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(99,102,241,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,.05) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
        }
        .orb {
            position: absolute; border-radius: 50%;
            filter: blur(80px); pointer-events: none;
        }
        .orb-1 { width:400px;height:400px;background:radial-gradient(circle,rgba(99,102,241,.12),transparent 70%);top:-100px;left:-100px; }
        .orb-2 { width:300px;height:300px;background:radial-gradient(circle,rgba(6,182,212,.1),transparent 70%);bottom:-80px;right:-80px; }

        .card {
            position: relative;
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
            animation: fadeUp .4s ease;
            box-shadow: 0 8px 32px rgba(99,102,241,.1);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), #8b5cf6);
        }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .card-header {
            padding: 32px 36px 24px;
            border-bottom: 1px solid #f1f5f9;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 20px;
            transition: color .2s;
        }
        .back-link:hover { color: var(--primary); }

        .card-header h2 {
            font-size: 22px; font-weight: 800; color: #0f172a;
            margin-bottom: 6px;
        }

        .card-header p {
            font-size: 13px; color: #64748b; line-height: 1.6;
        }

        .steps {
            display: flex;
            gap: 0;
            padding: 20px 36px;
            border-bottom: 1px solid #f1f5f9;
            background: #fafbff;
        }

        .step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            position: relative;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 14px; left: 50%;
            width: 100%; height: 1px;
            background: var(--border);
            z-index: 0;
        }

        .step-num {
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
            position: relative; z-index: 1;
            transition: all .3s;
        }

        .step.active .step-num  { background: var(--primary); color: #fff; box-shadow: 0 0 14px var(--primary-glow); }
        .step.done .step-num    { background: #10b981; color: #fff; }
        .step.pending .step-num { background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; }

        .step-label {
            font-size: 10px; font-weight: 600; letter-spacing: .5px;
            text-transform: uppercase;
            color: #94a3b8;
        }
        .step.active .step-label { color: #a5b4fc; }
        .step.done .step-label   { color: #6ee7b7; }

        /* FORM STEPS */
        .form-step { display: none; padding: 28px 36px 32px; }
        .form-step.active { display: block; animation: fadeIn .3s ease; }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 11px; font-weight: 700;
            color: #64748b;
            letter-spacing: 1.2px; text-transform: uppercase;
            margin-bottom: 8px;
        }
        .input-wrap { position: relative; }
        .input-wrap i.icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            font-size: 16px; color: #cbd5e1;
            pointer-events: none; transition: color .2s;
        }
        .input-wrap:focus-within i.icon { color: var(--primary); }

        .form-input {
            width: 100%;
            padding: 13px 14px 13px 44px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            color: #0f172a; font-size: 14px;
            font-family: var(--font);
            outline: none; transition: all .2s;
        }
        .form-input::placeholder { color: #cbd5e1; }
        .form-input:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }

        .toggle-pw {
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #94a3b8;
            cursor: pointer; font-size: 16px;
        }
        .toggle-pw:hover { color: #6366f1; }

        .hint-text {
            font-size: 12px; color: #94a3b8;
            margin-top: 6px; line-height: 1.5;
        }
        .hint-text strong { color: var(--primary); }

        .btn-primary {
            width: 100%; padding: 13px;
            background: var(--primary); border: none;
            border-radius: 10px; color: #fff;
            font-size: 14px; font-weight: 700;
            font-family: var(--font);
            cursor: pointer; transition: all .2s;
            display: flex; align-items: center;
            justify-content: center; gap: 8px;
            box-shadow: 0 4px 16px rgba(99,102,241,.3);
        }
        .btn-primary:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99,102,241,.4);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary:disabled { opacity:.6; pointer-events:none; }

        .alert-error {
            padding: 11px 14px;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            border-radius: 8px;
            font-size: 13px; color: #be123c;
            margin-bottom: 18px;
            display: flex; gap: 8px; align-items: flex-start;
        }

        .alert-success {
            padding: 11px 14px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            font-size: 13px; color: #15803d;
            margin-bottom: 18px;
            display: flex; gap: 8px; align-items: flex-start;
        }

        .pw-strength {
            margin-top: 8px;
            display: flex; gap: 4px; align-items: center;
        }
        .pw-strength-bar {
            flex: 1; height: 3px; border-radius: 2px;
            background: rgba(255,255,255,.08);
            transition: background .3s;
        }
        .pw-strength-label {
            font-size: 11px; color: #94a3b8;
            min-width: 50px; text-align: right;
        }

        @media (max-width: 480px) {
            .card-header, .form-step { padding-left: 20px; padding-right: 20px; }
            .steps { padding: 14px 20px; }
            .card-header h2 { font-size: 18px; }
        }

        @media (max-width: 768px) {
            body { align-items: flex-start; padding: 0; overflow: auto; background: #fff; }
            .card {
                max-width: 100%;
                min-height: 100vh;
                border-radius: 0;
                border: none;
                box-shadow: none;
            }
            .card::before { border-radius: 0; }
            .orb { display: none; }
        }
    </style>
</head>
<body>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="card">
    <div class="card-header">
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke Login
        </a>
        <h2>🔑 Reset Password</h2>
        <p>Verifikasi identitas Anda untuk mengatur ulang password akun.</p>
    </div>

    <!-- Steps indicator -->
    <div class="steps" id="steps-indicator">
        <div class="step active" id="step-ind-1">
            <div class="step-num">1</div>
            <div class="step-label">Verifikasi</div>
        </div>
        <div class="step pending" id="step-ind-2">
            <div class="step-num">2</div>
            <div class="step-label">Password Baru</div>
        </div>
        <div class="step pending" id="step-ind-3">
            <div class="step-num"><i class="bi bi-check"></i></div>
            <div class="step-label">Selesai</div>
        </div>
    </div>

    {{-- STEP 1: Verifikasi --}}
    <div class="form-step active" id="step-1">

        @if($errors->any())
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('password.reset.verify') }}" id="form-verify">
            @csrf

            <div class="form-group">
                <label class="form-label">Username</label>
                <div class="input-wrap">
                    <i class="bi bi-person icon"></i>
                    <input type="text" name="username" class="form-input"
                        placeholder="Masukkan username akun"
                        value="{{ old('username') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Lahir</label>
                <div class="input-wrap">
                    <i class="bi bi-calendar3 icon"></i>
                    <input type="date" name="tanggal_lahir" class="form-input"
                        value="{{ old('tanggal_lahir') }}" required
                        style="padding-left:44px;color-scheme:dark;">
                </div>
                <p class="hint-text">Sesuai data yang terdaftar di sistem</p>
            </div>

            <div class="form-group">
                <label class="form-label">Kunci Reset</label>
                <div class="input-wrap">
                    <i class="bi bi-shield-lock icon"></i>
                    <input type="text" name="reset_key" class="form-input"
                        placeholder="Masukkan kunci reset dari admin"
                        required autocomplete="off">
                </div>
                <p class="hint-text">Dapatkan <strong>kunci reset</strong> dari operator/admin sekolah</p>
            </div>

            <button type="submit" class="btn-primary" id="btn-verify">
                <i class="bi bi-shield-check"></i>
                Verifikasi Identitas
            </button>
        </form>
    </div>

    {{-- STEP 2: Password Baru (tampil jika verified) --}}
    @if(session('reset_token'))
    <div class="form-step active" id="step-2">
        <div class="alert-success">
            <i class="bi bi-check-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
            <span>Identitas terverifikasi. Silakan buat password baru.</span>
        </div>

        <form method="POST" action="{{ route('password.reset.update') }}" id="form-reset">
            @csrf
            <input type="hidden" name="reset_token" value="{{ session('reset_token') }}">

            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <div class="input-wrap">
                    <i class="bi bi-lock icon"></i>
                    <input type="password" name="password" class="form-input"
                        id="pw-new" placeholder="Minimal 8 karakter"
                        required minlength="8"
                        oninput="checkStrength(this.value)">
                    <button type="button" class="toggle-pw" onclick="togglePw('pw-new','icon-pw-new')">
                        <i class="bi bi-eye" id="icon-pw-new"></i>
                    </button>
                </div>
                <div class="pw-strength" id="pw-strength-wrap">
                    <div class="pw-strength-bar" id="bar-1"></div>
                    <div class="pw-strength-bar" id="bar-2"></div>
                    <div class="pw-strength-bar" id="bar-3"></div>
                    <div class="pw-strength-bar" id="bar-4"></div>
                    <span class="pw-strength-label" id="strength-label">—</span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock-fill icon"></i>
                    <input type="password" name="password_confirmation" class="form-input"
                        id="pw-confirm" placeholder="Ulangi password baru"
                        required minlength="8">
                    <button type="button" class="toggle-pw" onclick="togglePw('pw-confirm','icon-pw-confirm')">
                        <i class="bi bi-eye" id="icon-pw-confirm"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary" id="btn-reset">
                <i class="bi bi-check-circle"></i>
                Simpan Password Baru
            </button>
        </form>
    </div>
    @endif

    {{-- STEP 3: Selesai --}}
    @if(session('reset_success'))
    <div class="form-step active" id="step-3"
         style="text-align:center;padding-top:40px;padding-bottom:40px;">
        <div style="width:72px;height:72px;background:rgba(16,185,129,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:#10b981;">
            <i class="bi bi-check-lg"></i>
        </div>
        <h3 style="font-size:20px;font-weight:800;color:#fff;margin-bottom:8px;">Password Berhasil Diubah!</h3>
        <p style="font-size:13px;color:var(--muted);margin-bottom:28px;">Silakan login menggunakan password baru Anda.</p>
        <a href="{{ route('login') }}" class="btn-primary" style="text-decoration:none;max-width:240px;margin:0 auto;">
            <i class="bi bi-box-arrow-in-right"></i>
            Masuk Sekarang
        </a>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Aktifkan step indicator sesuai kondisi session
@if(session('reset_token'))
    document.getElementById('step-ind-1').classList.replace('active','done');
    document.getElementById('step-ind-1').querySelector('.step-num').innerHTML = '<i class="bi bi-check"></i>';
    document.getElementById('step-ind-2').classList.replace('pending','active');
    document.getElementById('step-1').classList.remove('active');
    document.getElementById('step-2').classList.add('active');
@endif
@if(session('reset_success'))
    ['step-ind-1','step-ind-2'].forEach(id => {
        const el = document.getElementById(id);
        el.classList.remove('active','pending');
        el.classList.add('done');
        el.querySelector('.step-num').innerHTML = '<i class="bi bi-check"></i>';
    });
    document.getElementById('step-ind-3').classList.replace('pending','active');
    document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
    document.getElementById('step-3').classList.add('active');
@endif

function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function checkStrength(val) {
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const colors  = ['#ef4444','#f59e0b','#6366f1','#10b981'];
    const labels  = ['Lemah','Cukup','Kuat','Sangat Kuat'];

    for (let i = 1; i <= 4; i++) {
        const bar = document.getElementById('bar-' + i);
        bar.style.background = i <= score ? colors[score-1] : 'rgba(255,255,255,.08)';
    }
    document.getElementById('strength-label').textContent = val.length ? labels[score-1] : '—';
    document.getElementById('strength-label').style.color = val.length ? colors[score-1] : 'var(--muted)';
}

// Loading state
document.getElementById('form-verify')?.addEventListener('submit', function() {
    const btn = document.getElementById('btn-verify');
    btn.disabled = true;
    btn.innerHTML = '<span style="width:14px;height:14px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;display:inline-block;animation:spin .6s linear infinite"></span> Memverifikasi...';
});

document.getElementById('form-reset')?.addEventListener('submit', function(e) {
    const pw  = document.getElementById('pw-new')?.value;
    const pw2 = document.getElementById('pw-confirm')?.value;
    if (pw !== pw2) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Password tidak cocok!', text:'Pastikan kedua password sama.', background:'#0d0d0d', color:'#fff', confirmButtonColor:'#6366f1' });
        return;
    }
    const btn = document.getElementById('btn-reset');
    btn.disabled = true;
    btn.innerHTML = '<span style="width:14px;height:14px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;display:inline-block;animation:spin .6s linear infinite"></span> Menyimpan...';
});
</script>
<style>@keyframes spin { to { transform:rotate(360deg); } }</style>
</body>
</html>
