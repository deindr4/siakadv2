@extends('layouts.app')

@section('page-title', 'Detail User')
@section('page-subtitle', 'Informasi lengkap pengguna')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>👤 Detail User</h1>
    <p>Informasi lengkap {{ $user->name }}</p>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-header">
        <h3>Profil Pengguna</h3>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm" style="background:#ede9fe;color:#6366f1;">
                <i class="bi bi-pencil-fill"></i> Edit
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">

        <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid #f1f5f9;">
            <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#f59e0b);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:26px;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 style="font-size:18px;font-weight:700;color:#0f172a;">{{ $user->name }}</h2>
                <p style="font-size:13px;color:#64748b;">{{ $user->email }}</p>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div style="background:#f8fafc;padding:14px;border-radius:10px;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.8px;">Role</p>
                <div style="margin-top:6px;">
                    @foreach($user->roles as $role)
                        <span class="badge badge-primary">
                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div style="background:#f8fafc;padding:14px;border-radius:10px;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.8px;">Status</p>
                <span class="badge badge-success" style="margin-top:6px;">Aktif</span>
            </div>
            <div style="background:#f8fafc;padding:14px;border-radius:10px;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.8px;">Terdaftar</p>
                <p style="font-size:13px;font-weight:600;color:#374151;margin-top:4px;">
                    {{ $user->created_at->format('d F Y') }}
                </p>
            </div>
            <div style="background:#f8fafc;padding:14px;border-radius:10px;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.8px;">Terakhir Diperbarui</p>
                <p style="font-size:13px;font-weight:600;color:#374151;margin-top:4px;">
                    {{ $user->updated_at->format('d F Y') }}
                </p>
            </div>
        </div>

        @if($user->id !== auth()->id())
        <div style="margin-top:24px;padding-top:24px;border-top:1px solid #f1f5f9;">
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-danger"
                    onclick="confirmDelete(this.closest('form'))">
                    <i class="bi bi-trash-fill"></i> Hapus User Ini
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection
