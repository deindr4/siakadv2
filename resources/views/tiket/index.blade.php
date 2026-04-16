@extends('layouts.app')
@section('page-title', 'Tiket Kritik, Saran & Pengaduan')

@section('content')
<style>
.tiket-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
.tiket-stat  { border-radius:14px; padding:14px 16px; display:flex; align-items:center; gap:12px; }
.tiket-stat i { font-size:26px; flex-shrink:0; }
.ts-label { font-size:10px; font-weight:700; text-transform:uppercase; }
.ts-val   { font-size:26px; font-weight:800; line-height:1; }

.tiket-filter { display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; }
.tf-field { display:flex; flex-direction:column; gap:4px; }
.tf-field label { font-size:11px; font-weight:700; text-transform:uppercase; color:#94a3b8; }
.tf-field select, .tf-field input { padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none; font-family:inherit; background:#fff; }

/* Desktop table */
.tiket-tbl-wrap { overflow-x:auto; }
.tiket-tbl { width:100%; border-collapse:collapse; }
.tiket-tbl th { padding:10px 14px; font-size:11px; font-weight:700; text-transform:uppercase; color:#94a3b8; border-bottom:2px solid #f1f5f9; white-space:nowrap; text-align:left; }
.tiket-tbl td { padding:12px 14px; font-size:13px; border-bottom:1px solid #f8fafc; vertical-align:middle; }
.tiket-tbl tr:last-child td { border-bottom:none; }
.tiket-tbl tr:hover td { background:#f8fafc; }

/* Mobile card */
.tiket-cards { display:none; }
.tiket-card-row { padding:14px 16px; border-bottom:1px solid #f1f5f9; text-decoration:none; color:inherit; display:block; transition:background .12s; }
.tiket-card-row:hover { background:#f8fafc; }
.tiket-card-row:last-child { border-bottom:none; }

@media(max-width:768px) {
    .tiket-stats { grid-template-columns:repeat(2,1fr); }
    .ts-val { font-size:22px; }
    .tiket-tbl-wrap { display:none; }
    .tiket-cards { display:block; }
    .tiket-filter { flex-direction:column; }
    .tf-field select, .tf-field input, .tf-field { width:100%; }
}
@media(max-width:480px) {
    .tiket-stats { gap:8px; }
    .tiket-stat { padding:12px; gap:8px; }
    .tiket-stat i { font-size:20px; }
    .ts-val { font-size:20px; }
}
</style>

<div class="page-title">
    <h1>🎫 Tiket Kritik, Saran & Pengaduan</h1>
    <p>{{ $isAdmin ? 'Kelola semua tiket masuk' : 'Buat dan pantau tiket Anda' }}</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:16px;color:#16a34a;font-weight:600;">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:16px;color:#dc2626;font-weight:600;">❌ {{ session('error') }}</div>
@endif

<div class="tiket-stats">
    @foreach([['Total',$stats->total,'#6366f1','#eef2ff','ticket-perforated-fill'],['Aktif',$stats->open,'#16a34a','#dcfce7','envelope-open-fill'],['Selesai',$stats->selesai,'#64748b','#f1f5f9','check-circle-fill'],['Terkunci',$stats->terkunci,'#dc2626','#fee2e2','lock-fill']] as [$l,$v,$c,$bg,$ic])
    <div class="tiket-stat" style="background:{{ $bg }};"><i class="bi bi-{{ $ic }}" style="color:{{ $c }};"></i><div><p class="ts-label" style="color:{{ $c }};">{{ $l }}</p><p class="ts-val" style="color:{{ $c }};">{{ $v }}</p></div></div>
    @endforeach
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET">
            <div class="tiket-filter">
                <div class="tf-field" style="min-width:140px;">
                    <label>Kategori</label>
                    <select name="kategori">
                        <option value="">Semua</option>
                        @foreach(\App\Models\Tiket::kategoriList() as $v => $l)
                        <option value="{{ $v }}" {{ request('kategori')==$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="tf-field" style="min-width:120px;">
                    <label>Status</label>
                    <select name="status">
                        <option value="">Semua</option>
                        @foreach(['open'=>'Terbuka','diproses'=>'Diproses','selesai'=>'Selesai','terkunci'=>'Terkunci'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="tf-field" style="flex:1;min-width:160px;">
                    <label>Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul tiket...">
                </div>
                <div style="display:flex;gap:8px;padding-top:18px;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                    <a href="{{ route('tiket.index') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i></a>
                    @if(auth()->user()->hasRole('siswa') || auth()->user()->hasRole('guru') || auth()->user()->hasRole('wakil_kepala_sekolah'))
                    <a href="{{ route('tiket.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle-fill"></i> Buat Tiket</a>
                    @endif
                    @if($isAdmin)
                    <a href="{{ route('laporan.tiket') }}" class="btn" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;"><i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">

        {{-- DESKTOP TABLE --}}
        <div class="tiket-tbl-wrap">
            <table class="tiket-tbl">
                <thead><tr>
                    <th>#</th><th>Judul Tiket</th><th>Kategori</th>
                    @if($isAdmin)<th>Dari</th>@endif
                    <th>Prioritas</th><th>Status</th>
                    <th style="text-align:center;">💬</th>
                    <th style="text-align:center;">Sisa</th>
                    <th>Tanggal</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                @forelse($tikets as $i => $t)
                <tr style="{{ $t->status==='terkunci'?'opacity:.7;':'' }}">
                    <td style="color:#94a3b8;font-size:12px;">{{ $tikets->firstItem()+$i }}</td>
                    <td><div style="font-weight:700;">{{ $t->status==='terkunci'?'🔒 ':'' }}{{ $t->judul }}</div><div style="font-size:11px;color:#94a3b8;">{{ \Str::limit($t->isi,55) }}</div></td>
                    <td><span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $t->kategoriColor() }}22;color:{{ $t->kategoriColor() }};">{{ $t->kategoriLabel() }}</span></td>
                    @if($isAdmin)
                    <td style="font-size:12px;">
                        @if($t->is_anonim)<span style="color:#64748b;">🎭 Anonim</span><div style="font-size:10px;color:#6366f1;font-style:italic;">{{ $t->user?->name }}</div>
                        @else {{ $t->user?->name }}<div style="font-size:10px;color:#94a3b8;">{{ ucfirst($t->role_pembuat) }}</div>@endif
                    </td>
                    @endif
                    <td><span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $t->prioritasColor() }}22;color:{{ $t->prioritasColor() }};">{{ ucfirst($t->prioritas) }}</span></td>
                    <td><span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $t->statusBg() }};color:{{ $t->statusColor() }};">{{ $t->statusLabel() }}</span></td>
                    <td style="text-align:center;font-weight:700;color:#6366f1;">{{ $t->respon->count() }}</td>
                    <td style="text-align:center;font-size:12px;">
                        @if($t->canReply()) @php $s=$t->sisaHariLock(); @endphp
                            <span style="font-weight:700;color:{{ $s<=1?'#dc2626':($s<=3?'#d97706':'#16a34a') }};">{{ $s }}h</span>
                        @else <span style="color:#94a3b8;">—</span> @endif
                    </td>
                    <td style="font-size:12px;white-space:nowrap;">{{ $t->created_at->translatedFormat('d M Y') }}</td>
                    <td><a href="{{ route('tiket.show',$t) }}" class="btn" style="padding:5px 12px;font-size:12px;background:#f0f9ff;color:#0284c7;"><i class="bi bi-eye-fill"></i> Lihat</a></td>
                </tr>
                @empty
                <tr><td colspan="{{ $isAdmin?10:9 }}" style="text-align:center;padding:50px;color:#94a3b8;">
                    <i class="bi bi-ticket-perforated" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                    {{ $isAdmin?'Belum ada tiket masuk':'Anda belum membuat tiket' }}
                </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE CARDS --}}
        <div class="tiket-cards">
            @forelse($tikets as $t)
            <a href="{{ route('tiket.show',$t) }}" class="tiket-card-row" style="{{ $t->status==='terkunci'?'opacity:.7;':'' }}">
                {{-- Baris 1: judul + status --}}
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:7px;">
                    <p style="font-weight:700;font-size:14px;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $t->status==='terkunci'?'🔒 ':'' }}{{ $t->judul }}
                    </p>
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;flex-shrink:0;background:{{ $t->statusBg() }};color:{{ $t->statusColor() }};">{{ $t->statusLabel() }}</span>
                </div>
                {{-- Baris 2: preview isi --}}
                <p style="font-size:12px;color:#64748b;margin-bottom:8px;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $t->isi }}</p>
                {{-- Baris 3: badge + meta --}}
                <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $t->kategoriColor() }}22;color:{{ $t->kategoriColor() }};">{{ $t->kategoriLabel() }}</span>
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $t->prioritasColor() }}22;color:{{ $t->prioritasColor() }};">{{ ucfirst($t->prioritas) }}</span>
                    @if($t->canReply()) @php $s=$t->sisaHariLock(); @endphp
                    <span style="font-size:11px;font-weight:700;color:{{ $s<=1?'#dc2626':($s<=3?'#d97706':'#16a34a') }};">⏱ {{ $s }}h</span>
                    @endif
                    <span style="font-size:11px;color:#94a3b8;margin-left:auto;"><i class="bi bi-chat-fill" style="color:#6366f1;"></i> {{ $t->respon->count() }} · {{ $t->created_at->translatedFormat('d M') }}</span>
                </div>
                @if($isAdmin)
                <p style="font-size:11px;color:#94a3b8;margin-top:5px;">
                    <i class="bi bi-person-fill"></i>
                    {{ $t->is_anonim ? '🎭 Anonim ('.$t->user?->name.')' : ($t->user?->name.' · '.ucfirst($t->role_pembuat)) }}
                </p>
                @endif
            </a>
            @empty
            <div style="padding:40px;text-align:center;color:#94a3b8;">
                <i class="bi bi-ticket-perforated" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                {{ $isAdmin?'Belum ada tiket masuk':'Anda belum membuat tiket' }}
            </div>
            @endforelse
        </div>

        @if($tikets->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">{{ $tikets->links() }}</div>
        @endif
    </div>
</div>
@endsection
