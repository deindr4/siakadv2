<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Alumni — SMA Negeri 1 Kuta Selatan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #0f2318 0%, #1a3a2a 50%, #14532d 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .top-bar {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.12);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .top-bar img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
        .top-bar-logo { width: 40px; height: 40px; border-radius: 8px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; }
        .top-bar h1 { font-size: 15px; font-weight: 700; color: #fff; }
        .top-bar p  { font-size: 11px; color: rgba(255,255,255,0.6); }
        .top-bar-right { margin-left: auto; }
        .top-bar-right a { color: rgba(255,255,255,0.7); font-size: 12px; text-decoration: none; }
        .top-bar-right a:hover { color: #fff; }

        /* Main */
        .main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 24px 16px; }

        .container { width: 100%; max-width: 960px; }

        /* Hero */
        .hero { text-align: center; margin-bottom: 32px; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(134,239,172,0.15); border: 1px solid rgba(134,239,172,0.3);
            color: rgba(255,255,255,0.9); font-size: 12px; font-weight: 600;
            padding: 5px 14px; border-radius: 99px; margin-bottom: 16px;
        }
        .hero h2 { font-size: clamp(22px,4vw,36px); font-weight: 800; color: #fff; margin-bottom: 10px; }
        .hero p  { font-size: 14px; color: rgba(255,255,255,0.65); max-width: 500px; margin: 0 auto; }

        /* Card Layout */
        .card-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }
        @media (max-width: 640px) { .card-wrap { grid-template-columns: 1fr; } }

        /* Card */
        .card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .card-header {
            background: linear-gradient(135deg, #166534, #14532d);
            padding: 18px 20px;
            color: #fff;
        }
        .card-header h3 { font-size: 15px; font-weight: 700; margin-bottom: 2px; }
        .card-header p  { font-size: 12px; opacity: 0.8; }
        .card-body { padding: 20px; }

        /* Form */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-group label span { color: #ef4444; }
        .form-control {
            width: 100%; padding: 10px 12px; border: 1.5px solid #e5e7eb;
            border-radius: 8px; font-size: 13px; color: #1f2937;
            transition: border-color .2s, box-shadow .2s; outline: none;
        }
        .form-control:focus { border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,0.12); }
        .form-hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }

        /* Button */
        .btn-verify {
            width: 100%; padding: 12px; background: linear-gradient(135deg, #16a34a, #15803d);
            color: #fff; border: none; border-radius: 10px; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: opacity .2s, transform .1s; margin-top: 4px;
        }
        .btn-verify:hover { opacity: 0.92; transform: translateY(-1px); }
        .btn-verify:active { transform: translateY(0); }

        /* Alert */
        .alert {
            padding: 12px 14px; border-radius: 10px; font-size: 13px;
            margin-bottom: 16px; display: flex; align-items: flex-start; gap: 8px;
        }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .alert-info  { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }

        /* Hasil */
        .result-card {
            background: #fff; border-radius: 16px; overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .result-header {
            background: linear-gradient(135deg, #059669, #047857);
            padding: 18px 20px; color: #fff; display: flex; align-items: center; gap: 12px;
        }
        .result-header .check-icon {
            width: 40px; height: 40px; background: rgba(255,255,255,0.2);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .result-header h3 { font-size: 15px; font-weight: 700; }
        .result-header p  { font-size: 12px; opacity: 0.8; }

        .result-body { padding: 20px; }
        .result-name {
            font-size: 20px; font-weight: 800; color: #1e293b;
            text-align: center; margin-bottom: 16px; padding-bottom: 16px;
            border-bottom: 2px dashed #e5e7eb;
        }
        .result-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
        }
        @media (max-width: 400px) { .result-grid { grid-template-columns: 1fr; } }

        .result-item { background: #f8fafc; border-radius: 8px; padding: 10px 12px; }
        .result-item .label { font-size: 10px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .result-item .value { font-size: 13px; color: #1e293b; font-weight: 600; }
        .result-item.highlight { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .result-item.highlight .label { color: #16a34a; }
        .result-item.highlight .value { color: #15803d; }
        .result-item.full { grid-column: 1 / -1; }

        .verified-badge {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            background: #dcfce7; border: 1px solid #bbf7d0; color: #15803d;
            font-size: 12px; font-weight: 700; padding: 8px; border-radius: 8px;
            margin-top: 14px;
        }

        /* Info box */
        .info-box {
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px; padding: 16px; margin-top: 20px;
        }
        .info-box h4 { font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.9); margin-bottom: 10px; }
        .info-box ul { list-style: none; }
        .info-box li { font-size: 11px; color: rgba(255,255,255,0.6); padding: 3px 0; display: flex; gap: 6px; }
        .info-box li::before { content: '•'; color: #86efac; flex-shrink: 0; }

        /* Footer */
        footer { text-align: center; padding: 16px; color: rgba(255,255,255,0.4); font-size: 11px; }
    </style>
</head>
<body>

{{-- Top Bar --}}
<div class="top-bar">
    <div class="top-bar-logo"><i class="bi bi-mortarboard-fill" style="color:#a5b4fc;font-size:20px"></i></div>
    <div>
        <h1>SMA Negeri 1 Kuta Selatan</h1>
        <p>Sistem Informasi Akademik</p>
    </div>
    <div class="top-bar-right">
        <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Login SIAKAD</a>
    </div>
</div>

<div class="main">
    <div class="container">

        {{-- Hero --}}
        <div class="hero">
            <div class="hero-badge">
                <i class="bi bi-patch-check-fill" style="color:#86efac"></i>
                Verifikasi Resmi
            </div>
            <h2>Pencarian & Verifikasi Data Alumni</h2>
            <p>Masukkan data diri Anda untuk memverifikasi status kelulusan dan melihat data ijazah.</p>
        </div>

        <div class="card-wrap">

            {{-- Form --}}
            <div class="card">
                <div class="card-header">
                    <h3><i class="bi bi-search me-2"></i>Form Verifikasi</h3>
                    <p>Isi ketiga data di bawah ini dengan benar</p>
                </div>
                <div class="card-body">

                    @if(session('error'))
                    <div class="alert alert-error">
                        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    @endif

                    @if(!session('alumni'))
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                        <span>Data Anda harus cocok persis dengan data yang tercatat di sekolah.</span>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('public.alumni.verify') }}">
                        @csrf
                        <div class="form-group">
                            <label>NISN <span>*</span></label>
                            <input type="text" name="nisn" class="form-control"
                                   placeholder="Contoh: 0012345678"
                                   value="{{ old('nisn') }}" required
                                   maxlength="20">
                            <p class="form-hint">
                                Nomor Induk Siswa Nasional
                                @if($errors->has('nisn'))
                                    <span style="color:#ef4444;display:block">{{ $errors->first('nisn') }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Lahir <span>*</span></label>
                            <input type="date" name="tanggal_lahir" class="form-control"
                                   value="{{ old('tanggal_lahir') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Nomor Ijazah <span>*</span></label>
                            <input type="text" name="no_ijazah" class="form-control"
                                   placeholder="Contoh: DN-Ma 0012345"
                                   value="{{ old('no_ijazah') }}" required maxlength="50">
                            <p class="form-hint">Tertera di lembar ijazah Anda</p>
                        </div>
                        <button type="submit" class="btn-verify">
                            <i class="bi bi-search me-2"></i>Verifikasi Sekarang
                        </button>
                    </form>
                </div>
            </div>

            {{-- Hasil --}}
            @if(session('alumni'))
            @php $a = session('alumni'); @endphp
            <div class="result-card">
                <div class="result-header">
                    <div class="check-icon">✅</div>
                    <div>
                        <h3>Data Alumni Ditemukan</h3>
                        <p>Data telah diverifikasi oleh sistem</p>
                    </div>
                </div>
                <div class="result-body">
                    <div class="result-name">{{ $a['nama'] }}</div>
                    <div class="result-grid">
                        <div class="result-item highlight full">
                            <div class="label">Status</div>
                            <div class="value">✅ Alumni Terverifikasi — Lulus Tahun {{ $a['tahun_lulus'] ?? '-' }}</div>
                        </div>
                        <div class="result-item">
                            <div class="label">NISN</div>
                            <div class="value">{{ $a['nisn'] }}</div>
                        </div>
                        <div class="result-item">
                            <div class="label">Jenis Kelamin</div>
                            <div class="value">{{ $a['jenis_kelamin'] }}</div>
                        </div>
                        <div class="result-item">
                            <div class="label">Tempat Lahir</div>
                            <div class="value">{{ $a['tempat_lahir'] ?? '-' }}</div>
                        </div>
                        <div class="result-item">
                            <div class="label">Tanggal Lahir</div>
                            <div class="value">{{ $a['tanggal_lahir'] ? \Carbon\Carbon::parse($a['tanggal_lahir'])->translatedFormat('d F Y') : '-' }}</div>
                        </div>
                        <div class="result-item">
                            <div class="label">Kelas / Rombel</div>
                            <div class="value">{{ $a['nama_rombel'] ?? '-' }}</div>
                        </div>
                        <div class="result-item">
                            <div class="label">Tanggal Lulus</div>
                            <div class="value">{{ $a['tanggal_lulus'] ? \Carbon\Carbon::parse($a['tanggal_lulus'])->translatedFormat('d F Y') : '-' }}</div>
                        </div>
                        <div class="result-item highlight">
                            <div class="label">Nomor Ijazah</div>
                            <div class="value">{{ $a['no_ijazah'] ?? '-' }}</div>
                        </div>
                        <div class="result-item highlight">
                            <div class="label">Nomor SKHUN</div>
                            <div class="value">{{ $a['no_skhun'] ?? '-' }}</div>
                        </div>
                        @if($a['nilai_rata'])
                        <div class="result-item">
                            <div class="label">Nilai Rata-rata</div>
                            <div class="value">{{ number_format($a['nilai_rata'], 2) }}</div>
                        </div>
                        @endif
                        <div class="result-item">
                            <div class="label">Kurikulum</div>
                            <div class="value">{{ $a['kurikulum'] ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="verified-badge">
                        <i class="bi bi-patch-check-fill"></i>
                        Diverifikasi oleh Sistem SIAKAD SMA Negeri 1 Kuta Selatan
                    </div>
                </div>
            </div>
            @else
            {{-- Info box kalau belum ada hasil --}}
            <div class="info-box">
                <h4><i class="bi bi-shield-lock-fill me-2" style="color:#86efac"></i>Keamanan & Privasi</h4>
                <ul>
                    <li>Data hanya ditampilkan jika ketiga informasi cocok</li>
                    <li>Pencarian dibatasi 10x per menit per pengguna</li>
                    <li>Informasi sensitif (NIK, alamat, HP) tidak ditampilkan</li>
                    <li>Aktivitas pencarian dicatat oleh sistem</li>
                    <li>Untuk keperluan resmi, hubungi TU sekolah</li>
                </ul>
            </div>
            @endif

        </div>

    </div>
</div>

<footer>
    &copy; {{ date('Y') }} SMA Negeri 1 Kuta Selatan — Sistem Informasi Akademik
</footer>

</body>
</html>
