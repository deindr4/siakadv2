@extends('layouts.app')
@section('page-title', 'Rekap Net Poin Siswa')
@section('sidebar-menu') @include('partials.sidebar_bk') @endsection
@section('content')

<div class="page-title">
    <h1>📊 Rekap Net Poin Siswa</h1>
    <p>Poin pelanggaran dikurangi poin kebaikan</p>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:2;min-width:160px;">
                <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:3px;">CARI SISWA</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama siswa..."
                    style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;">
            </div>
            <div style="flex:1;min-width:130px;">
                <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:3px;">KELAS</label>
                <select name="rombel" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#fff;" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($rombels as $r)
                    <option value="{{ $r->rombongan_belajar_id }}" {{ request('rombel') == $r->rombongan_belajar_id ? 'selected' : '' }}>
                        {{ $r->nama_rombel }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:130px;">
                <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:3px;">SEMESTER</label>
                <select name="semester_id" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#fff;" onchange="this.form.submit()">
                    @foreach($semesters as $s)
                    <option value="{{ $s->id }}" {{ $semesterId == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
            <a href="{{ route('bk.poin-positif.index') }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Net Poin per Siswa</h3>
        <span style="font-size:12px;color:#94a3b8;">{{ $siswaList->total() }} siswa</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Siswa</th>
                        <th style="text-align:center;color:#dc2626;">Poin Pelanggaran</th>
                        <th style="text-align:center;color:#16a34a;">Poin Kebaikan</th>
                        <th style="text-align:center;">Net Poin</th>
                        <th style="text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaList as $i => $s)
                    @php
                        $netColor = $s->net_poin >= 75 ? '#dc2626' : ($s->net_poin >= 50 ? '#d97706' : ($s->net_poin >= 25 ? '#0284c7' : '#16a34a'));
                        $netBg    = $s->net_poin >= 75 ? '#fee2e2' : ($s->net_poin >= 50 ? '#fef3c7' : ($s->net_poin >= 25 ? '#e0f2fe' : '#f0fdf4'));
                    @endphp
                    <tr>
                        <td>{{ $siswaList->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:600;font-size:13px;">{{ $s->nama }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $s->nama_rombel ?? '-' }}</div>
                        </td>
                        <td style="text-align:center;">
                            <span style="background:#fee2e2;color:#dc2626;font-weight:700;padding:3px 10px;border-radius:20px;">
                                {{ $s->poin_pelanggaran }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span style="background:#dcfce7;color:#15803d;font-weight:700;padding:3px 10px;border-radius:20px;">
                                +{{ $s->poin_positif }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span style="background:{{ $netBg }};color:{{ $netColor }};font-weight:800;padding:4px 12px;border-radius:20px;font-size:14px;">
                                {{ $s->net_poin }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            @if($s->net_poin >= 75)
                                <span class="badge badge-danger">⚠️ Kritis</span>
                            @elseif($s->net_poin >= 50)
                                <span class="badge badge-warning">Tinggi</span>
                            @elseif($s->net_poin >= 25)
                                <span class="badge" style="background:#e0f2fe;color:#0284c7;">Sedang</span>
                            @else
                                <span class="badge badge-success">Baik</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswaList->hasPages())
        <div style="padding:14px 16px;border-top:1px solid #f1f5f9;">{{ $siswaList->links() }}</div>
        @endif
    </div>
</div>
@endsection
