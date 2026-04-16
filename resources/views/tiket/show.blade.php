@extends('layouts.app')
@section('page-title', 'Detail Tiket')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection
@section('content')
<style>
.tc-wrap{display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start}
.tc-main{display:flex;flex-direction:column;border-radius:14px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.08)}
.tc-head{background:linear-gradient(135deg,#16a34a,#4ade80);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.tc-head-title{font-size:14px;font-weight:700;color:#fff;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.tc-head-sub{font-size:11px;color:#bbf7d0;margin-top:3px}
.tc-body{background:#f1f5f9;padding:20px 16px;min-height:400px;max-height:65vh;overflow-y:auto;display:flex;flex-direction:column;gap:14px;scroll-behavior:smooth}
.tc-foot{background:#fff;border-top:1px solid #e2e8f0;padding:14px 16px}
.msg-wrap{display:flex;gap:8px;align-items:flex-end}
.msg-wrap.r{flex-direction:row-reverse}
.msg-av{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;color:#6366f1}
.msg-av.u{background:#e0e7ff}
.msg-av.a{background:#ede9fe}
/* Per-role avatar colors */
.msg-av.role-admin{background:#dbeafe;color:#1d4ed8}
.msg-av.role-kepala{background:#fef3c7;color:#d97706}
.msg-av.role-waka{background:#f0fdf4;color:#15803d}
.msg-av.role-guru{background:#fce7f3;color:#be185d}
.msg-av.role-bk{background:#fef9c3;color:#a16207}
.msg-av.role-tu{background:#f1f5f9;color:#475569}
/* Per-role bubble colors */
.msg-bubble.role-admin{background:#2563eb;color:#fff;border-bottom-right-radius:4px}
.msg-bubble.role-kepala{background:#d97706;color:#fff;border-bottom-right-radius:4px}
.msg-bubble.role-waka{background:#16a34a;color:#fff;border-bottom-right-radius:4px}
.msg-bubble.role-guru{background:#be185d;color:#fff;border-bottom-right-radius:4px}
.msg-bubble.role-bk{background:#a16207;color:#fff;border-bottom-right-radius:4px}
.msg-bubble.role-tu{background:#475569;color:#fff;border-bottom-right-radius:4px}
/* Per-role name color */
.msg-name.role-admin{color:#2563eb}
.msg-name.role-kepala{color:#d97706}
.msg-name.role-waka{color:#16a34a}
.msg-name.role-guru{color:#be185d}
.msg-name.role-bk{color:#a16207}
.msg-name.role-tu{color:#475569}
/* Per-role badge */
.msg-badge.role-admin{background:#dbeafe;color:#1d4ed8}
.msg-badge.role-kepala{background:#fef3c7;color:#d97706}
.msg-badge.role-waka{background:#dcfce7;color:#15803d}
.msg-badge.role-guru{background:#fce7f3;color:#be185d}
.msg-badge.role-bk{background:#fef9c3;color:#a16207}
.msg-badge.role-tu{background:#f1f5f9;color:#475569}
.msg-box{max-width:70%;display:flex;flex-direction:column;gap:3px}
.msg-wrap.r .msg-box{align-items:flex-end}
.msg-name{font-size:10px;font-weight:700;color:#64748b;padding:0 4px}
.msg-wrap.r .msg-name{color:#6366f1;text-align:right}
.msg-bubble{padding:10px 14px;border-radius:16px;font-size:13px;line-height:1.6;word-break:break-word;white-space:pre-wrap}
.msg-bubble.u{background:#fff;border:1px solid #e2e8f0;border-bottom-left-radius:4px;color:#1e293b}
.msg-bubble.a{background:#6366f1;color:#fff;border-bottom-right-radius:4px}
.msg-bubble.first{border:1.5px solid #6366f1;background:#fff}
.msg-bubble img{max-width:100%;max-height:180px;border-radius:8px;margin-top:8px;display:block}
.msg-time{font-size:10px;color:#94a3b8;padding:0 4px}
.msg-wrap.r .msg-time{text-align:right}
.msg-badge{font-size:9px;background:#eef2ff;color:#6366f1;padding:1px 6px;border-radius:10px;margin-left:4px}
.date-sep{display:flex;align-items:center;gap:8px;margin:4px 0}
.date-sep span{font-size:11px;color:#94a3b8;background:#e2e8f0;padding:3px 12px;border-radius:20px;white-space:nowrap}
.date-sep::before,.date-sep::after{content:'';flex:1;height:1px;background:#cbd5e1}
.sys-msg{text-align:center;align-self:center;padding:6px 16px;border-radius:20px;font-size:11px;font-weight:600}
.sys-msg.done{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0}
.sys-msg.lock{background:#fee2e2;color:#dc2626;border:1px solid #fecaca}
.tc-input-wrap{display:flex;gap:10px;align-items:flex-end}
.tc-textarea{flex:1;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;outline:none;font-family:inherit;resize:none;min-height:44px;max-height:120px;overflow-y:auto;line-height:1.5;transition:border-color .15s}
.tc-textarea:focus{border-color:#16a34a}
.tc-send{width:44px;height:44px;border-radius:12px;background:#16a34a;color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;transition:background .15s,transform .1s}
.tc-send:hover{background:#15803d}
.tc-send:active{transform:scale(.94)}
.tc-extras{display:flex;align-items:center;gap:12px;margin-top:10px;flex-wrap:wrap;font-size:12px;color:#64748b}
.tc-file-preview{font-size:11px;color:#6366f1;background:#eef2ff;padding:3px 10px;border-radius:20px;display:none}
.tc-closed{background:#fff;padding:14px 16px;text-align:center}
.sb-card{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.07)}
.sb-head{padding:12px 16px;border-bottom:1px solid #f1f5f9;font-size:13px;font-weight:700;color:#374151;display:flex;align-items:center;gap:8px}
.sb-body{padding:8px 16px}
.info-r{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:12px}
.info-r:last-child{border:none}
.info-lbl{font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase}
.badge-status{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block}
@media(max-width:900px){
  .tc-wrap{grid-template-columns:1fr}
  .msg-box{max-width:85%}
  .tc-body{max-height:55vh}
}
@media(max-width:480px){
  .msg-box{max-width:90%}
  .tc-head-title{font-size:13px}
}
</style>

{{-- Header Halaman --}}
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:16px;">
  <div class="page-title" style="margin:0;">
    <h1 style="font-size:17px;display:flex;align-items:center;gap:8px;">
      <i class="bi bi-ticket-perforated-fill" style="color:#6366f1;"></i>
      Tiket #{{ $tiket->id }}
    </h1>
    <p>{{ $tiket->created_at->translatedFormat('d M Y, H:i') }}</p>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap;">
    <a href="{{ route('tiket.index') }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
    @if($isAdmin && $tiket->canReply())
    <form method="POST" action="{{ route('tiket.tutup',$tiket) }}" onsubmit="return confirm('Tandai tiket ini selesai?')" style="display:inline;">
      @csrf @method('PATCH')
      <button type="submit" class="btn btn-sm" style="background:#dcfce7;color:#16a34a;">
        <i class="bi bi-check-circle-fill"></i> Selesai
      </button>
    </form>
    @endif
    @if($isAdmin && $tiket->isLocked())
    <button onclick="document.getElementById('modal-buka').style.display='flex'" class="btn btn-sm" style="background:#fef3c7;color:#d97706;">
      <i class="bi bi-unlock-fill"></i> Buka
    </button>
    @endif
  </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:14px;color:#16a34a;font-weight:600;font-size:13px;">
  <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:14px;color:#dc2626;font-weight:600;font-size:13px;">
  <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
</div>
@endif

<div class="tc-wrap">

  {{-- Kiri: Chat --}}
  <div class="tc-main">

    {{-- Header Chat --}}
    <div class="tc-head">
      <div style="flex:1;min-width:0;">
        <div class="tc-head-title">{{ $tiket->judul }}</div>
        <div class="tc-head-sub">
          <span style="background:rgba(255,255,255,.2);color:#fff;padding:1px 8px;border-radius:10px;font-size:10px;">
            {{ $tiket->kategoriLabel() }}
          </span>
          &nbsp;·&nbsp;{{ $tiket->respon->count() + 1 }} pesan
        </div>
      </div>
      <span class="badge-status" style="background:{{ $tiket->statusBg() }};color:{{ $tiket->statusColor() }};">
        {{ $tiket->statusLabel() }}
      </span>
    </div>

    {{-- Body Chat --}}
    <div class="tc-body" id="chatBody">

      @php $prevDate = null; @endphp
      @php $firstDate = $tiket->created_at->translatedFormat('d M Y'); @endphp
      <div class="date-sep"><span>{{ $firstDate }}</span></div>
      @php $prevDate = $firstDate; @endphp

      {{-- Pesan pertama --}}
      <div class="msg-wrap">
        <div class="msg-av u">
          @if($tiket->is_anonim && !$isAdmin)
            <i class="bi bi-person-fill-slash"></i>
          @else
            <i class="bi bi-person-fill"></i>
          @endif
        </div>
        <div class="msg-box">
          <span class="msg-name">
            {{ $tiket->namaDisplay($isAdmin) }}
            @if($tiket->is_anonim && $isAdmin)
              <span style="font-size:10px;color:#94a3b8;">(anonim)</span>
            @endif
          </span>
          <div class="msg-bubble u first">
            {{ $tiket->isi }}
            @if($tiket->fotoUrl())
            <a href="{{ $tiket->fotoUrl() }}" target="_blank">
              <img src="{{ $tiket->fotoUrl() }}" alt="foto">
            </a>
            @endif
          </div>
          <span class="msg-time">{{ $tiket->created_at->format('H:i') }}</span>
        </div>
      </div>

      {{-- Respon --}}
      @foreach($tiket->respon as $r)
        @php
          $fromAdmin = $r->isFromAdmin();
          $rDate = $r->created_at->translatedFormat('d M Y');
          $role = $r->role_responder ?? '';
          $roleClass = match($role) {
            'admin'                => 'role-admin',
            'kepala_sekolah'       => 'role-kepala',
            'wakil_kepala_sekolah' => 'role-waka',
            'guru'                 => 'role-guru',
            'bk'                  => 'role-bk',
            'tata_usaha'           => 'role-tu',
            default                => 'role-admin',
          };
          $roleLabel = match($role) {
            'admin'                => 'Admin',
            'kepala_sekolah'       => 'Kepala Sekolah',
            'wakil_kepala_sekolah' => 'Waka',
            'guru'                 => 'Guru',
            'bk'                  => 'BK',
            'tata_usaha'           => 'Tata Usaha',
            default                => ucfirst($role),
          };
        @endphp

        @if($rDate !== $prevDate)
          <div class="date-sep"><span>{{ $rDate }}</span></div>
          @php $prevDate = $rDate; @endphp
        @endif

        <div class="msg-wrap {{ $fromAdmin ? 'r' : '' }}">
          <div class="msg-av {{ $fromAdmin ? $roleClass : 'u' }}">
            @if($fromAdmin)
              <i class="bi bi-shield-fill"></i>
            @elseif($r->is_anonim && !$isAdmin)
              <i class="bi bi-person-fill-slash"></i>
            @else
              <i class="bi bi-person-fill"></i>
            @endif
          </div>
          <div class="msg-box">
            <span class="msg-name {{ $fromAdmin ? $roleClass : '' }}">
              {{ $r->namaDisplay($isAdmin) }}
              @if($fromAdmin)
                <span class="msg-badge {{ $roleClass }}">{{ $roleLabel }}</span>
              @endif
            </span>
            <div class="msg-bubble {{ $fromAdmin ? $roleClass : 'u' }}">
              {{ $r->isi }}
              @if($r->fotoUrl())
              <a href="{{ $r->fotoUrl() }}" target="_blank">
                <img src="{{ $r->fotoUrl() }}" alt="foto">
              </a>
              @endif
            </div>
            <span class="msg-time">{{ $r->created_at->format('H:i') }}</span>
          </div>
        </div>
      @endforeach

      {{-- Status sistem --}}
      @if($tiket->isLocked())
        <div class="sys-msg lock">
          <i class="bi bi-lock-fill me-1"></i> Tiket terkunci — tidak ada aktivitas 7 hari
        </div>
      @elseif($tiket->status === 'selesai')
        <div class="sys-msg done">
          <i class="bi bi-check-circle-fill me-1"></i>
          Selesai oleh {{ $tiket->closedBy?->name }}
          &middot; {{ $tiket->closed_at?->translatedFormat('d M Y, H:i') }}
        </div>
      @endif

    </div>

    {{-- Footer Chat --}}
    @if($tiket->canReply())
    <div class="tc-foot">
      <form method="POST" action="{{ route('tiket.respon',$tiket) }}" enctype="multipart/form-data" id="chatForm">
        @csrf @method('PATCH')
        <div class="tc-input-wrap">
          <textarea name="isi" class="tc-textarea" id="chatInput"
            placeholder="Tulis pesan..." required rows="1"
            oninput="autoResize(this)"></textarea>
          <button type="submit" class="tc-send" title="Kirim">
            <i class="bi bi-send-fill"></i>
          </button>
        </div>
        <div class="tc-extras">
          <label style="cursor:pointer;" title="Lampirkan foto">
            <i class="bi bi-paperclip" style="font-size:17px;color:#94a3b8;"></i>
            <input type="file" name="foto" accept=".jpg,.jpeg,.png" style="display:none;" onchange="prevFile(this)">
          </label>
          <span class="tc-file-preview" id="filePreview"></span>
          @if(!$isAdmin && auth()->user()->hasRole('siswa'))
          <label style="display:flex;align-items:center;gap:5px;cursor:pointer;">
            <input type="checkbox" name="is_anonim" value="1"> Anonim
          </label>
          @endif
          <span style="margin-left:auto;font-size:11px;color:#cbd5e1;">
            <i class="bi bi-send-fill" style="color:#6366f1;"></i> untuk kirim
          </span>
        </div>
      </form>
    </div>
    @else
    <div class="tc-closed">
      @if($tiket->isLocked())
        <p style="font-size:13px;color:#dc2626;">
          <i class="bi bi-lock-fill me-1"></i>Tiket terkunci
          @if($isAdmin) &mdash; klik <strong>Buka</strong> untuk melanjutkan @endif
        </p>
      @else
        <p style="font-size:13px;color:#16a34a;">
          <i class="bi bi-check-circle-fill me-1"></i>Tiket sudah selesai
        </p>
      @endif
    </div>
    @endif

  </div>

  {{-- Kanan: Sidebar --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Info --}}
    <div class="sb-card">
      <div class="sb-head">
        <i class="bi bi-info-circle-fill" style="color:#6366f1;"></i> Info Tiket
      </div>
      <div class="sb-body">
        <div class="info-r">
          <span class="info-lbl">Status</span>
          <span class="badge-status" style="background:{{ $tiket->statusBg() }};color:{{ $tiket->statusColor() }};">{{ $tiket->statusLabel() }}</span>
        </div>
        <div class="info-r">
          <span class="info-lbl">Prioritas</span>
          <span style="color:{{ $tiket->prioritasColor() }};font-weight:700;">
            <i class="bi bi-circle-fill" style="font-size:8px;"></i> {{ ucfirst($tiket->prioritas) }}
          </span>
        </div>
        <div class="info-r">
          <span class="info-lbl">Kategori</span>
          <span style="font-size:11px;background:{{ $tiket->kategoriColor() }}22;color:{{ $tiket->kategoriColor() }};padding:2px 8px;border-radius:6px;font-weight:600;">{{ $tiket->kategoriLabel() }}</span>
        </div>
        <div class="info-r">
          <span class="info-lbl">Pesan</span>
          <span style="font-weight:800;color:#6366f1;font-size:16px;">{{ $tiket->respon->count() + 1 }}</span>
        </div>
        @if($tiket->canReply())
        @php $sisa = $tiket->sisaHariLock(); @endphp
        <div class="info-r">
          <span class="info-lbl">Auto-lock</span>
          <span style="font-weight:700;color:{{ $sisa<=1 ? '#dc2626' : ($sisa<=3 ? '#d97706' : '#16a34a') }};">
            {{ $sisa }} hari
          </span>
        </div>
        @endif
        <div class="info-r">
          <span class="info-lbl">Dibuat</span>
          <span style="color:#374151;">{{ $tiket->created_at->translatedFormat('d M Y') }}</span>
        </div>
        <div class="info-r">
          <span class="info-lbl">Pengirim</span>
          <span style="color:#374151;">
            @if($tiket->is_anonim && !$isAdmin)
              <i class="bi bi-person-fill-slash me-1"></i>Anonim
            @else
              {{ $tiket->user?->name ?? '-' }}
            @endif
          </span>
        </div>
      </div>
    </div>

    {{-- Prioritas (admin) --}}
    @if($isAdmin)
    <div class="sb-card">
      <div class="sb-head">
        <i class="bi bi-flag-fill" style="color:#6366f1;"></i> Update Prioritas
      </div>
      <div style="padding:12px 16px;">
        <form method="POST" action="{{ route('tiket.prioritas',$tiket) }}">
          @csrf @method('PATCH')
          <select name="prioritas" onchange="this.form.submit()"
            style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#fff;font-family:inherit;">
            <option value="rendah" {{ $tiket->prioritas=='rendah'?'selected':'' }}>Rendah</option>
            <option value="sedang" {{ $tiket->prioritas=='sedang'?'selected':'' }}>Sedang</option>
            <option value="tinggi" {{ $tiket->prioritas=='tinggi'?'selected':'' }}>Tinggi</option>
          </select>
        </form>
      </div>
    </div>
    @endif

    {{-- Aksi (admin) --}}
    @if($isAdmin)
    <div class="sb-card">
      <div class="sb-head">
        <i class="bi bi-lightning-fill" style="color:#6366f1;"></i> Aksi
      </div>
      <div style="padding:12px 16px;display:flex;flex-direction:column;gap:8px;">
        @if($tiket->canReply())
        <form method="POST" action="{{ route('tiket.tutup',$tiket) }}" onsubmit="return confirm('Tandai selesai?')">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-sm" style="width:100%;background:#dcfce7;color:#16a34a;">
            <i class="bi bi-check-circle-fill me-1"></i>Tandai Selesai
          </button>
        </form>
        @endif
        @if($tiket->isLocked())
        <button onclick="document.getElementById('modal-buka').style.display='flex'"
          class="btn btn-sm" style="width:100%;background:#fef3c7;color:#d97706;">
          <i class="bi bi-unlock-fill me-1"></i>Buka Kunci
        </button>
        @endif
      </div>
    </div>
    @endif

  </div>
</div>

{{-- Modal Buka Kunci --}}
<div id="modal-buka" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:16px;">
  <div style="background:#fff;border-radius:16px;width:100%;max-width:420px;max-height:90vh;overflow-y:auto;">
    <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
      <h3 style="font-size:15px;font-weight:700;margin:0;display:flex;align-items:center;gap:8px;">
        <i class="bi bi-unlock-fill" style="color:#d97706;"></i> Buka Kunci Tiket
      </h3>
      <button onclick="document.getElementById('modal-buka').style.display='none'"
        style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;line-height:1;">&times;</button>
    </div>
    <div style="padding:20px;">
      <form method="POST" action="{{ route('tiket.buka',$tiket) }}">
        @csrf @method('PATCH')
        <div style="display:flex;flex-direction:column;gap:12px;">
          <div>
            <label style="font-size:12px;font-weight:700;display:block;margin-bottom:4px;">ALASAN (opsional)</label>
            <textarea name="alasan" rows="3" placeholder="cth: Perlu tindak lanjut lebih lanjut..."
              style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;resize:none;box-sizing:border-box;"></textarea>
          </div>
          <div style="display:flex;gap:10px;">
            <button type="button" onclick="document.getElementById('modal-buka').style.display='none'"
              class="btn" style="flex:1;background:#f1f5f9;color:#374151;">Batal</button>
            <button type="submit" class="btn btn-primary" style="flex:1;">
              <i class="bi bi-unlock-fill"></i> Buka Kunci
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
var chatBody = document.getElementById('chatBody');
if(chatBody) chatBody.scrollTop = chatBody.scrollHeight;

function autoResize(el){
  el.style.height='auto';
  el.style.height=Math.min(el.scrollHeight,120)+'px';
}

function prevFile(input){
  var p=document.getElementById('filePreview');
  if(input.files&&input.files[0]){
    p.textContent=input.files[0].name;
    p.style.display='inline-block';
  }else{
    p.style.display='none';
  }
}

var modalBuka=document.getElementById('modal-buka');
if(modalBuka){
  modalBuka.addEventListener('click',function(e){
    if(e.target===this) this.style.display='none';
  });
}
</script>
@endsection
