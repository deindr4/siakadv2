@extends('layouts.app')
@section('title', 'Pelanggaran Saya')
@section('sidebar-menu') @include('partials.sidebar_siswa') @endsection
@section('content')
@include('partials._dashboard_responsive')

<div class="page-title">
    <h1>⚠️ Pelanggaran & Poin Saya</h1>
    <p>{{ $siswa?->nama }} &mdash; {{ $siswa?->nama_rombel ?? '-' }}</p>
</div>

{{-- Filter Semester --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <label style="font-size:13px;font-weight:600;color:#374151;margin-bottom:0;">Semester:</label>
            <select name="semester_id" class="form-select form-select-sm" style="width:auto;max-width:250px;" onchange="this.form.submit()">
                @foreach($semesters as $s)
                    <option value="{{ $s->id }}" {{ $semesterId == $s->id ? 'selected' : '' }}>
                        {{ $s->nama }}{{ $s->is_aktif ? ' (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- NET POIN — Hero Card --}}
@php
    $netColor = match($warningLevel) {
        'danger'  => '#dc2626',
        'warning' => '#d97706',
        'info'    => '#0284c7',
        default   => '#16a34a',
    };
    $netBg = match($warningLevel) {
        'danger'  => 'linear-gradient(135deg,#fef2f2,#fee2e2)',
        'warning' => 'linear-gradient(135deg,#fffbeb,#fef3c7)',
        'info'    => 'linear-gradient(135deg,#f0f9ff,#e0f2fe)',
        default   => 'linear-gradient(135deg,#f0fdf4,#dcfce7)',
    };
@endphp
<div class="card" style="margin-bottom:20px;background:{{ $netBg }};border:2px solid {{ $netColor }}22;">
    <div class="card-body" style="padding:20px;">
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div style="text-align:center;flex-shrink:0;">
                <div style="font-size:48px;font-weight:900;color:{{ $netColor }};line-height:1;">{{ $netPoin }}</div>
                <div style="font-size:12px;font-weight:700;color:{{ $netColor }};opacity:.8;">NET POIN</div>
            </div>
            <div style="flex:1;min-width:200px;">
                <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:12px;">
                    <div>
                        <div style="font-size:11px;color:#94a3b8;font-weight:600;">POIN PELANGGARAN</div>
                        <div style="font-size:22px;font-weight:800;color:#dc2626;">{{ $totalPoin }}</div>
                    </div>
                    <div style="font-size:24px;color:#94a3b8;align-self:center;">−</div>
                    <div>
                        <div style="font-size:11px;color:#94a3b8;font-weight:600;">POIN KEBAIKAN</div>
                        <div style="font-size:22px;font-weight:800;color:#16a34a;">{{ $totalPoinPositif }}</div>
                    </div>
                    <div style="font-size:24px;color:#94a3b8;align-self:center;">=</div>
                    <div>
                        <div style="font-size:11px;color:#94a3b8;font-weight:600;">NET POIN</div>
                        <div style="font-size:22px;font-weight:800;color:{{ $netColor }};">{{ $netPoin }}</div>
                    </div>
                </div>
                {{-- Progress bar --}}
                <div style="background:rgba(0,0,0,.08);border-radius:99px;height:10px;margin-bottom:6px;">
                    <div style="background:{{ $netColor }};width:{{ min($netPoin,100) }}%;height:10px;border-radius:99px;transition:width .4s;"></div>
                </div>
                <div style="font-size:12px;color:{{ $netColor }};font-weight:600;">
                    @if($warningLevel === 'danger') ⚠️ Poin sangat tinggi! Segera konsultasi dengan BK.
                    @elseif($warningLevel === 'warning') ⚠️ Poin cukup tinggi. Harap perhatikan perilaku.
                    @elseif($warningLevel === 'info') ℹ️ Poin dalam batas wajar. Tetap jaga perilaku.
                    @else ✅ Poin masih rendah. Pertahankan perilaku yang baik!
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats 4 Kotak --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    @foreach([
        ['bi-exclamation-circle-fill', '#dc2626', '#fee2e2', $totalPoin,        'Poin Pelanggaran'],
        ['bi-star-fill',               '#d97706', '#fef3c7', $totalPoinPositif,  'Poin Kebaikan'],
        ['bi-list-ul',                 '#6366f1', '#eef2ff', $totalKasus,        'Total Kasus'],
        ['bi-check-circle-fill',       '#16a34a', '#dcfce7', $kasusSelesai,      'Kasus Selesai'],
    ] as [$icon, $color, $bg, $val, $label])
    <div class="card" style="border:0;">
        <div style="background:{{ $bg }};border-radius:12px;padding:16px;text-align:center;">
            <i class="bi {{ $icon }}" style="font-size:20px;color:{{ $color }};display:block;margin-bottom:6px;"></i>
            <div style="font-size:26px;font-weight:800;color:{{ $color }};line-height:1;">{{ $val }}</div>
            <div style="font-size:11px;font-weight:600;color:{{ $color }};margin-top:4px;opacity:.85;">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Tab: Pelanggaran | Kebaikan --}}
<div style="display:flex;gap:0;margin-bottom:20px;background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.04);">
    @foreach([['pelanggaran','⚠️ Pelanggaran',$totalKasus],['kebaikan','⭐ Poin Kebaikan',$riwayatPositif->count()]] as [$key,$label,$count])
    <a href="#{{ $key }}" onclick="switchTab('{{ $key }}')"
        id="tab-{{ $key }}"
        style="flex:1;padding:12px 20px;text-align:center;font-size:13px;font-weight:600;text-decoration:none;cursor:pointer;
               border-bottom:3px solid {{ $key==='pelanggaran' ? '#dc2626' : 'transparent' }};
               color:{{ $key==='pelanggaran' ? '#dc2626' : '#64748b' }};
               background:{{ $key==='pelanggaran' ? '#fef2f2' : '#fff' }};transition:all .2s;">
        {{ $label }}
        <span style="display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:999px;font-size:11px;font-weight:700;margin-left:6px;
                     background:{{ $key==='pelanggaran' ? '#dc2626' : '#f1f5f9' }};
                     color:{{ $key==='pelanggaran' ? '#fff' : '#64748b' }};">{{ $count }}</span>
    </a>
    @endforeach
</div>

{{-- Panel Pelanggaran --}}
<div id="panel-pelanggaran">
    <div class="card">
        <div class="card-header">
            <h3>⚠️ Daftar Pelanggaran</h3>
            <span style="font-size:12px;background:#fee2e2;color:#dc2626;padding:2px 10px;border-radius:20px;font-weight:700;">{{ $pelanggaran->total() }} kasus</span>
        </div>
        <div class="card-body" style="padding:0;">
            @if($pelanggaran->isEmpty())
                <div style="text-align:center;padding:48px 20px;color:#94a3b8;">
                    <i class="bi bi-shield-check" style="font-size:3rem;color:#22c55e;display:block;margin-bottom:10px;"></i>
                    <p style="font-weight:600;margin-bottom:4px;">Tidak ada pelanggaran!</p>
                    <p style="font-size:13px;">Pertahankan perilaku yang baik 👍</p>
                </div>
            @else
                @foreach($pelanggaran as $p)
                @php
                    $pc = $p->poin >= 25 ? '#dc2626' : ($p->poin >= 15 ? '#d97706' : '#6366f1');
                    $pb = $p->poin >= 25 ? '#fee2e2' : ($p->poin >= 15 ? '#fef3c7' : '#eef2ff');
                    $kat = $p->jenisPelanggaran?->kategori;
                    $katColor = $kat === 'berat' ? '#dc2626' : ($kat === 'sedang' ? '#d97706' : '#0284c7');
                    $katBg    = $kat === 'berat' ? '#fee2e2' : ($kat === 'sedang' ? '#fef3c7' : '#e0f2fe');
                @endphp
                <div style="display:flex;align-items:flex-start;gap:14px;padding:16px;{{ !$loop->last ? 'border-bottom:1px solid #f1f5f9;' : '' }}">
                    <div style="flex-shrink:0;text-align:center;">
                        <div style="width:46px;height:46px;border-radius:50%;background:{{ $pb }};color:{{ $pc }};font-weight:800;font-size:16px;display:flex;align-items:center;justify-content:center;">
                            {{ $p->poin }}
                        </div>
                        <p style="font-size:10px;color:#94a3b8;margin-top:3px;">poin</p>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                            <span style="font-weight:700;font-size:13px;color:#1e293b;">{{ $p->jenisPelanggaran?->nama ?? '-' }}</span>
                            <span style="font-size:11px;padding:1px 8px;border-radius:20px;font-weight:600;background:{{ $katBg }};color:{{ $katColor }};">{{ ucfirst($kat ?? '-') }}</span>
                        </div>
                        @if($p->keterangan)<p style="font-size:12px;color:#64748b;margin-bottom:4px;">{{ $p->keterangan }}</p>@endif
                        @if($p->tindakan)<p style="font-size:12px;color:#16a34a;margin-bottom:4px;"><i class="bi bi-check2-circle me-1"></i>Tindakan: {{ $p->tindakan }}</p>@endif
                        <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:11px;color:#94a3b8;">
                            <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}</span>
                            <span><i class="bi bi-person me-1"></i>{{ $p->dicatatOleh?->name ?? '-' }}</span>
                            <span>
                                @if($p->status === 'selesai') <i class="bi bi-check-circle-fill" style="color:#16a34a;"></i> Selesai
                                @else <i class="bi bi-clock-fill" style="color:#d97706;"></i> Aktif @endif
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($pelanggaran->hasPages())
                <div style="padding:12px 16px;border-top:1px solid #f1f5f9;">{{ $pelanggaran->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Panel Kebaikan --}}
<div id="panel-kebaikan" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>⭐ Riwayat Poin Kebaikan</h3>
            <span style="font-size:12px;background:#fef3c7;color:#d97706;padding:2px 10px;border-radius:20px;font-weight:700;">{{ $riwayatPositif->count() }} kegiatan</span>
        </div>
        <div class="card-body" style="padding:0;">
            @if($riwayatPositif->isEmpty())
                <div style="text-align:center;padding:48px 20px;color:#94a3b8;">
                    <i class="bi bi-star" style="font-size:3rem;color:#fbbf24;display:block;margin-bottom:10px;"></i>
                    <p style="font-weight:600;margin-bottom:4px;">Belum ada poin kebaikan.</p>
                    <p style="font-size:13px;">Ikuti kegiatan positif untuk mengurangi poin pelanggaran!</p>
                </div>
            @else
                @foreach($riwayatPositif as $r)
                @php
                    $kat = $r->jenisKegiatan?->kategori;
                    $katLabel = App\Models\JenisKegiatanPositif::kategoriList()[$kat] ?? $kat;
                @endphp
                <div style="display:flex;align-items:flex-start;gap:14px;padding:16px;{{ !$loop->last ? 'border-bottom:1px solid #f1f5f9;' : '' }}">
                    <div style="flex-shrink:0;text-align:center;">
                        <div style="width:46px;height:46px;border-radius:50%;background:#dcfce7;color:#15803d;font-weight:800;font-size:15px;display:flex;align-items:center;justify-content:center;">
                            +{{ $r->poin }}
                        </div>
                        <p style="font-size:10px;color:#94a3b8;margin-top:3px;">poin</p>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                            <span style="font-weight:700;font-size:13px;color:#1e293b;">{{ $r->jenisKegiatan?->nama ?? '-' }}</span>
                            <span style="font-size:11px;padding:1px 8px;border-radius:20px;font-weight:600;background:#f0fdf4;color:#16a34a;">{{ $katLabel }}</span>
                        </div>
                        @if($r->keterangan)<p style="font-size:12px;color:#64748b;margin-bottom:4px;">{{ $r->keterangan }}</p>@endif
                        <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:11px;color:#94a3b8;">
                            <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d F Y') }}</span>
                            <span><i class="bi bi-person me-1"></i>Dicatat oleh: {{ $r->dicatatOleh?->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<script>
function switchTab(active) {
    ['pelanggaran','kebaikan'].forEach(key => {
        const tab   = document.getElementById('tab-' + key);
        const panel = document.getElementById('panel-' + key);
        const isActive = key === active;
        panel.style.display = isActive ? 'block' : 'none';
        tab.style.borderBottom = isActive
            ? '3px solid ' + (key === 'pelanggaran' ? '#dc2626' : '#d97706')
            : '3px solid transparent';
        tab.style.color = isActive
            ? (key === 'pelanggaran' ? '#dc2626' : '#d97706')
            : '#64748b';
        tab.style.background = isActive
            ? (key === 'pelanggaran' ? '#fef2f2' : '#fffbeb')
            : '#fff';
        // badge
        const badge = tab.querySelector('span');
        if (badge) {
            badge.style.background = isActive
                ? (key === 'pelanggaran' ? '#dc2626' : '#d97706')
                : '#f1f5f9';
            badge.style.color = isActive ? '#fff' : '#64748b';
        }
    });
}
</script>
@endsection
