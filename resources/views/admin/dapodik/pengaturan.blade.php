@extends('layouts.app')

@section('page-title', 'Pengaturan Dapodik')
@section('page-subtitle', 'Konfigurasi koneksi ke server Dapodik')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<style>
    .dapodik-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        align-items: start;
    }

    .form-label-custom {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .input-custom {
        width: 100%;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13.5px;
        outline: none;
        font-family: inherit;
        border: 1.5px solid #e2e8f0;
        transition: border-color 0.2s;
    }

    .input-custom:focus {
        border-color: #6366f1;
    }

    .input-error {
        border-color: #ef4444 !important;
    }

    .help-text {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
    }

    .error-text {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
    }

    @media (max-width: 992px) {
        .dapodik-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-title">
    <h1>⚙️ Pengaturan Dapodik</h1>
    <p>Konfigurasi IP, NPSN, dan Bearer Token untuk koneksi ke server Dapodik</p>
</div>

<div class="dapodik-container">

    {{-- KIRI: FORM PENGATURAN --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="bi bi-sliders" style="color:#6366f1;"></i> Konfigurasi Koneksi</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.dapodik.pengaturan.store') }}" method="POST">
                @csrf

                {{-- IP Address --}}
                <div style="margin-bottom:18px;">
                    <label class="form-label-custom">
                        IP Address Server Dapodik <span style="color:red;">*</span>
                    </label>
                    <input type="text" name="ip_address"
                        value="{{ old('ip_address', $pengaturan?->ip_address) }}"
                        placeholder="Contoh: 172.16.2.202"
                        class="input-custom @error('ip_address') input-error @enderror">
                    @error('ip_address') <p class="error-text">{{ $message }}</p> @enderror
                    <p class="help-text">IP server tempat Dapodik terinstall</p>
                </div>

                {{-- Port --}}
                <div style="margin-bottom:18px;">
                    <label class="form-label-custom">
                        Port <span style="color:red;">*</span>
                    </label>
                    <input type="text" name="port"
                        value="{{ old('port', $pengaturan?->port ?? '5774') }}"
                        placeholder="5774"
                        class="input-custom @error('port') input-error @enderror">
                    @error('port') <p class="error-text">{{ $message }}</p> @enderror
                    <p class="help-text">Default port Dapodik: 5774</p>
                </div>

                {{-- NPSN --}}
                <div style="margin-bottom:18px;">
                    <label class="form-label-custom">
                        NPSN <span style="color:red;">*</span>
                    </label>
                    <input type="text" name="npsn"
                        value="{{ old('npsn', $pengaturan?->npsn) }}"
                        placeholder="Contoh: 50103452"
                        class="input-custom @error('npsn') input-error @enderror">
                    @error('npsn') <p class="error-text">{{ $message }}</p> @enderror
                    <p class="help-text">Nomor Pokok Sekolah Nasional</p>
                </div>

                {{-- Bearer Token --}}
                <div style="margin-bottom:24px;">
                    <label class="form-label-custom">
                        Bearer Token <span style="color:red;">*</span>
                    </label>
                    <textarea name="bearer_token" id="bearerToken" rows="4"
                        placeholder="Masukkan Bearer Token dari Dapodik..."
                        class="input-custom @error('bearer_token') input-error @enderror"
                        style="font-family:monospace; font-size:12px; resize:vertical;">{{ old('bearer_token', $pengaturan?->bearer_token) }}</textarea>
                    @error('bearer_token') <p class="error-text">{{ $message }}</p> @enderror
                    <p class="help-text">Token didapat dari login Dapodik → menu API/Token</p>
                </div>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save-fill"></i> Simpan Pengaturan
                    </button>
                    <button type="button" class="btn" id="btnTestKoneksi" style="background:#f1f5f9; color:#374151;">
                        <i class="bi bi-wifi"></i> Test Koneksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KANAN: STATUS, PANDUAN, & ENDPOINT --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Status Koneksi --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-activity" style="color:#10b981;"></i> Status Koneksi</h3>
            </div>
            <div class="card-body">
                @if($pengaturan)
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                        <div style="width:44px; height:44px; background:#d1fae5; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px;">
                            ✅
                        </div>
                        <div>
                            <p style="font-weight:700; color:#065f46; font-size:14px; margin:0;">Pengaturan Tersimpan</p>
                            <p style="font-size:12px; color:#94a3b8; margin:0;">Terakhir diubah: {{ $pengaturan->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div style="background:#f8fafc; border-radius:10px; padding:14px; display:flex; flex-direction:column; gap:10px;">
                        <div style="display:flex; justify-content:space-between; font-size:13px;">
                            <span style="color:#64748b;">IP Address</span>
                            <strong>{{ $pengaturan->ip_address }}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px;">
                            <span style="color:#64748b;">Port</span>
                            <strong>{{ $pengaturan->port }}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px;">
                            <span style="color:#64748b;">NPSN</span>
                            <strong>{{ $pengaturan->npsn }}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px;">
                            <span style="color:#64748b;">Base URL</span>
                            <strong style="font-size:11px; color:#6366f1;">{{ $pengaturan->base_url }}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px;">
                            <span style="color:#64748b;">Sync Terakhir</span>
                            <strong>{{ $pengaturan->last_sync ? $pengaturan->last_sync->diffForHumans() : 'Belum pernah' }}</strong>
                        </div>
                    </div>

                    <div id="testResult" style="display:none; margin-top:14px; padding:12px 14px; border-radius:8px; font-size:13px; font-weight:600;"></div>
                @else
                    <div style="text-align:center; padding:20px;">
                        <i class="bi bi-wifi-off" style="font-size:36px; color:#e2e8f0; display:block; margin-bottom:8px;"></i>
                        <p style="color:#94a3b8; font-size:13px;">Belum ada pengaturan tersimpan</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Panduan --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-question-circle" style="color:#f59e0b;"></i> Cara Mendapatkan Token</h3>
            </div>
            <div class="card-body">
                <ol style="padding-left:18px; font-size:13px; color:#374151; line-height:2;">
                    <li>Buka aplikasi Dapodik di komputer sekolah</li>
                    <li>Login dengan akun operator sekolah</li>
                    <li>Buka menu <strong>Pengaturan → Web Service</strong></li>
                    <li>Cari bagian <strong>Token / API Key</strong></li>
                    <li>Copy token dan paste di form sebelah kiri</li>
                </ol>
                <div style="background:#fef3c7; border-radius:8px; padding:10px 14px; margin-top:12px; font-size:12px; color:#92400e;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Token bersifat rahasia, jangan dibagikan ke orang lain!
                </div>
            </div>
        </div>

        {{-- Endpoint API --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-link-45deg" style="color:#6366f1;"></i> Endpoint API Dapodik</h3>
            </div>
            <div class="card-body">
                @php
                    $base = $pengaturan ? $pengaturan->base_url . '/WebService' : 'http://[IP]:[PORT]/WebService';
                    $endpoints = [
                        ['getSekolah', '🏫', 'Data Sekolah'],
                        ['getPesertaDidik', '👨‍🎓', 'Data Siswa'],
                        ['getGtk', '👨‍🏫', 'Data GTK/Guru'],
                        ['getRombonganBelajar', '🏠', 'Data Rombel'],
                        ['getPengguna', '👤', 'Data Pengguna'],
                    ];
                @endphp
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @foreach($endpoints as [$endpoint, $icon, $label])
                    <div style="background:#f8fafc; border-radius:8px; padding:8px 12px; border:1px solid #f1f5f9;">
                        <div style="font-size:12px; font-weight:600; color:#374151;">{{ $icon }} {{ $label }}</div>
                        <div style="font-size:10px; color:#6366f1; font-family:monospace; margin-top:2px; word-break:break-all;">
                            {{ $base }}/{{ $endpoint }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btnTestKoneksi').addEventListener('click', function() {
    const btn = this;
    const resultDiv = document.getElementById('testResult');

    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengecek...';
    btn.disabled = true;

    fetch('{{ route("admin.dapodik.test") }}')
        .then(r => r.json())
        .then(data => {
            resultDiv.style.display = 'block';
            if (data.status) {
                resultDiv.style.background = '#d1fae5';
                resultDiv.style.color = '#065f46';
                resultDiv.innerHTML = '✅ ' + data.message;
            } else {
                resultDiv.style.background = '#fee2e2';
                resultDiv.style.color = '#991b1b';
                resultDiv.innerHTML = '❌ ' + data.message;
            }
        })
        .catch(() => {
            resultDiv.style.display = 'block';
            resultDiv.style.background = '#fee2e2';
            resultDiv.style.color = '#991b1b';
            resultDiv.innerHTML = '❌ Gagal menghubungi server!';
        })
        .finally(() => {
            btn.innerHTML = '<i class="bi bi-wifi"></i> Test Koneksi';
            btn.disabled = false;
        });
});
</script>
@endpush
