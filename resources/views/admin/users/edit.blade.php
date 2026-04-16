@extends('layouts.app')

@section('page-title', 'Edit User')
@section('page-subtitle', 'Perbarui data pengguna')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>✏️ Edit User</h1>
    <p>Perbarui data {{ $user->name }}</p>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-header">
        <h3>Form Edit User</h3>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf @method('PUT')

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Nama Lengkap <span style="color:red;">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid {{ $errors->has('name') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13.5px;outline:none;font-family:inherit;">
                @error('name')<p style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Email <span style="color:red;">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid {{ $errors->has('email') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13.5px;outline:none;font-family:inherit;">
                @error('email')<p style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Role <span style="color:red;">*</span>
                </label>
                <select name="role"
                    style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13.5px;outline:none;font-family:inherit;background:#fff;">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
                @error('role')<p style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Password Baru
                    <span style="font-weight:400;color:#94a3b8;">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password"
                    placeholder="Minimal 6 karakter"
                    style="width:100%;padding:10px 14px;border:1.5px solid {{ $errors->has('password') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13.5px;outline:none;font-family:inherit;">
                @error('password')<p style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Konfirmasi Password Baru
                </label>
                <input type="password" name="password_confirmation"
                    placeholder="Ulangi password baru"
                    style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13.5px;outline:none;font-family:inherit;">
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Perbarui User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn" style="background:#f1f5f9;color:#374151;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
