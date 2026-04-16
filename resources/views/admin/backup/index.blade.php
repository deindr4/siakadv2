@extends('layouts.app')

@section('title', 'Backup & Restore Database')

@section('content')
<style>
    .bm-page { padding: 1.25rem; max-width: 900px; margin: 0 auto; }

    /* Header */
    .bm-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .bm-title { font-size: 20px; font-weight: 600; color: #111827; }
    .bm-subtitle { display: flex; gap: 6px; align-items: center; margin-top: 6px; flex-wrap: wrap; }
    .db-badge { display: inline-flex; align-items: center; gap: 5px; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; padding: 3px 9px; font-size: 12px; font-weight: 500; color: #4b5563; font-family: 'Consolas', monospace; }

    .btn-primary-bm { display: inline-flex; align-items: center; gap: 6px; background: #3C3489; color: #fff; border: none; border-radius: 8px; padding: 9px 16px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; white-space: nowrap; }
    .btn-primary-bm:hover { background: #26215C; color: #fff; text-decoration: none; }

    /* Section label */
    .section-label { font-size: 11px; font-weight: 600; color: #9ca3af; letter-spacing: .06em; text-transform: uppercase; margin-bottom: 10px; }

    /* Alert */
    .bm-alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 10px; border: 1px solid; margin-bottom: 1rem; font-size: 13px; }
    .bm-alert.success { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .bm-alert.danger  { background: #fef2f2; border-color: #fca5a5; color: #991b1b; }
    .bm-alert-close { margin-left: auto; background: none; border: none; font-size: 18px; cursor: pointer; color: inherit; opacity: .6; }
    .bm-alert-close:hover { opacity: 1; }

    /* Status grid */
    .status-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-bottom: 1.25rem; }
    @media (max-width: 600px) { .status-grid { grid-template-columns: 1fr; } }

    .status-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 14px 16px; position: relative; overflow: hidden; }
    .status-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
    .status-card.ok::before   { background: #1D9E75; }
    .status-card.warn::before { background: #BA7517; }
    .status-card.err::before  { background: #E24B4A; }

    .status-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; font-size: 16px; }
    .status-icon.ok   { background: #E1F5EE; color: #0F6E56; }
    .status-icon.warn { background: #FAEEDA; color: #854F0B; }
    .status-icon.err  { background: #FCEBEB; color: #A32D2D; }

    .status-name { font-size: 12px; color: #6b7280; }
    .status-val  { font-size: 15px; font-weight: 600; color: #111827; margin-top: 2px; display: flex; align-items: center; gap: 5px; }
    .status-meta { font-size: 11px; color: #9ca3af; margin-top: 3px; }

    .status-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
    .dot-ok { background: #1D9E75; }
    .dot-warn { background: #EF9F27; }
    .dot-err { background: #E24B4A; }

    .status-pill { display: inline-flex; align-items: center; font-size: 11px; font-weight: 500; padding: 2px 8px; border-radius: 99px; margin-top: 5px; }
    .pill-ok   { background: #E1F5EE; color: #0F6E56; }
    .pill-warn { background: #FAEEDA; color: #854F0B; }
    .pill-err  { background: #FCEBEB; color: #A32D2D; }

    /* Backup table card */
    .bm-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 1rem; }
    .bm-card-head { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
    .bm-card-title { font-size: 13px; font-weight: 600; color: #111827; }
    .count-badge { background: #EEEDFE; color: #3C3489; font-size: 11px; font-weight: 600; padding: 2px 9px; border-radius: 99px; }

    .backup-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .backup-table th { font-size: 11px; color: #9ca3af; font-weight: 600; text-align: left; padding: 9px 14px; background: #f9fafb; border-bottom: 1px solid #f3f4f6; letter-spacing: .04em; text-transform: uppercase; }
    .backup-table td { padding: 11px 14px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; color: #374151; }
    .backup-table tr:last-child td { border-bottom: none; }
    .backup-table tr:hover td { background: #f8faff; }
    .backup-table tr.latest-row td { background: #f0f9ff; }

    .file-name-cell { display: flex; align-items: center; gap: 10px; }
    .file-ico { width: 34px; height: 34px; border-radius: 8px; background: #FAEEDA; color: #854F0B; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
    .fname { font-family: 'Consolas', 'Monaco', monospace; font-size: 12px; font-weight: 600; color: #111827; word-break: break-all; }
    .badge-latest { background: #EAF3DE; color: #3B6D11; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 99px; margin-left: 6px; white-space: nowrap; }
    .size-chip { font-family: 'Consolas', monospace; font-size: 11px; color: #6b7280; }
    .date-txt  { font-size: 12px; color: #6b7280; white-space: nowrap; }

    .actions { display: flex; gap: 5px; justify-content: flex-end; }
    .btn-ico { width: 30px; height: 30px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; cursor: pointer; transition: all .15s; color: #6b7280; }
    .btn-ico:hover { cursor: pointer; }
    .btn-ico.dl:hover { border-color: #AFA9EC; color: #534AB7; background: #EEEDFE; }
    .btn-ico.re:hover { border-color: #FAC775; color: #854F0B; background: #FAEEDA; }
    .btn-ico.rm:hover { border-color: #F09595; color: #A32D2D; background: #FCEBEB; }

    .empty-state { text-align: center; padding: 3rem 1rem; }
    .empty-icon { width: 56px; height: 56px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 12px; }

    .table-footer { padding: 9px 14px; background: #f9fafb; border-top: 1px solid #f3f4f6; font-size: 11px; color: #9ca3af; }

    /* Bottom row */
    .bottom-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    @media (max-width: 600px) { .bottom-row { grid-template-columns: 1fr; } }

    .bottom-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
    .bottom-card-head { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
    .bottom-card-body { padding: 14px 16px; }

    .head-ico { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; font-size: 15px; }
    .head-ico.amber { background: #FAEEDA; }
    .head-ico.teal  { background: #E1F5EE; }

    .file-drop { border: 1px dashed #d1d5db; border-radius: 8px; padding: 18px; text-align: center; margin-bottom: 12px; cursor: pointer; transition: background .15s; }
    .file-drop:hover { background: #f9fafb; }
    .file-drop small { display: block; font-size: 12px; color: #6b7280; margin-top: 4px; }

    .btn-restore-run { width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px; background: #FAEEDA; color: #633806; border: 1px solid #FAC775; border-radius: 8px; padding: 9px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
    .btn-restore-run:hover { background: #FAC775; }

    .info-list { list-style: none; padding: 0; }
    .info-list li { display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: #4b5563; padding: 6px 0; border-bottom: 1px solid #f3f4f6; }
    .info-list li:last-child { border-bottom: none; }
    .info-list code { font-family: 'Consolas', monospace; font-size: 11px; background: #f3f4f6; padding: 1px 5px; border-radius: 4px; }

    @media (max-width: 640px) {
        .col-date, .col-size { display: none; }
    }
</style>

<div class="bm-page">

    {{-- Page Header --}}
    <div class="bm-header">
        <div>
            <div class="bm-title">
                <i class="bi bi-database-fill-gear me-2" style="color:#3C3489;"></i>Database Manager
            </div>
            <div class="bm-subtitle">
                <span class="db-badge"><i class="bi bi-hdd-fill me-1"></i>{{ $dbInfo['database'] }}</span>
                <span class="db-badge"><i class="bi bi-hdd-network me-1"></i>{{ $dbInfo['host'] }}</span>
            </div>
        </div>
        <form id="form-backup" action="{{ route('admin.backup.create') }}" method="POST">
            @csrf
            <button type="button" class="btn-primary-bm" onclick="confirmBackup()">
                <i class="bi bi-plus-lg"></i> Buat Backup Baru
            </button>
        </form>
    </div>

    {{-- Flash Alert --}}
    @if(session('success') || session('error'))
        <div class="bm-alert {{ session('success') ? 'success' : 'danger' }}">
            <i class="bi bi-{{ session('success') ? 'check-circle-fill' : 'exclamation-triangle-fill' }}" style="font-size:16px;flex-shrink:0;"></i>
            <span>{!! session('success') ?? session('error') !!}</span>
            <button class="bm-alert-close" onclick="this.closest('.bm-alert').remove()">&times;</button>
        </div>
    @endif

    {{-- System Status --}}
    <div class="section-label">Status Sistem</div>
    <div class="status-grid">

        {{-- Database --}}
        <div class="status-card {{ $systemStatus['db']['ok'] ? 'ok' : 'err' }}">
            <div class="status-icon {{ $systemStatus['db']['ok'] ? 'ok' : 'err' }}">
                <i class="bi bi-database"></i>
            </div>
            <div class="status-name">Database</div>
            <div class="status-val">
                <span class="status-dot {{ $systemStatus['db']['ok'] ? 'dot-ok' : 'dot-err' }}"></span>
                {{ $systemStatus['db']['version'] ?? 'MySQL' }}
            </div>
            <span class="status-pill {{ $systemStatus['db']['ok'] ? 'pill-ok' : 'pill-err' }}">
                {{ $systemStatus['db']['ok'] ? 'Connected' : 'Error' }}
            </span>
            <div class="status-meta">Uptime: {{ $systemStatus['db']['uptime'] ?? '-' }}</div>
        </div>

        {{-- Redis --}}
        <div class="status-card {{ $systemStatus['redis']['ok'] ? 'ok' : 'err' }}">
            <div class="status-icon {{ $systemStatus['redis']['ok'] ? 'ok' : 'err' }}">
                <i class="bi bi-lightning-charge"></i>
            </div>
            <div class="status-name">Redis</div>
            <div class="status-val">
                <span class="status-dot {{ $systemStatus['redis']['ok'] ? 'dot-ok' : 'dot-err' }}"></span>
                {{ $systemStatus['redis']['version'] ?? 'Redis' }}
            </div>
            <span class="status-pill {{ $systemStatus['redis']['ok'] ? 'pill-ok' : 'pill-err' }}">
                {{ $systemStatus['redis']['ok'] ? 'Running' : 'Offline' }}
            </span>
            <div class="status-meta">Mem: {{ $systemStatus['redis']['memory'] ?? '-' }}</div>
        </div>

        {{-- Active Connections --}}
        <div class="status-card {{ $systemStatus['conn']['ok'] ? 'ok' : 'warn' }}">
            <div class="status-icon {{ $systemStatus['conn']['ok'] ? 'ok' : 'warn' }}">
                <i class="bi bi-diagram-3"></i>
            </div>
            <div class="status-name">Koneksi DB</div>
            <div class="status-val">
                <span class="status-dot {{ $systemStatus['conn']['ok'] ? 'dot-ok' : 'dot-warn' }}"></span>
                {{ $systemStatus['conn']['active'] ?? 0 }} aktif
            </div>
            <span class="status-pill {{ $systemStatus['conn']['ok'] ? 'pill-ok' : 'pill-warn' }}">
                {{ $systemStatus['conn']['active'] ?? 0 }} / {{ $systemStatus['conn']['max'] ?? 100 }} pool
            </span>
            <div class="status-meta">Driver: {{ $systemStatus['conn']['driver'] ?? 'PDO MySQL' }}</div>
        </div>

    </div>

    {{-- Backup File List --}}
    <div class="section-label">Riwayat Backup</div>
    <div class="bm-card">
        <div class="bm-card-head">
            <span class="bm-card-title"><i class="bi bi-clock-history me-2"></i>File Backup Database</span>
            <span class="count-badge">{{ $files->count() }} files</span>
        </div>

        @if($files->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-folder2-open"></i></div>
                <p style="color:#6b7280;font-size:13px;">Belum ada file backup yang tersimpan.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="backup-table">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th class="col-date">Tanggal</th>
                            <th class="col-size">Ukuran</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $i => $file)
                        <tr class="{{ $i === 0 ? 'latest-row' : '' }}">
                            <td>
                                <div class="file-name-cell">
                                    <div class="file-ico"><i class="bi bi-filetype-sql"></i></div>
                                    <div>
                                        <span class="fname" title="{{ $file->name }}">{{ $file->name }}</span>
                                        @if($i === 0)
                                            <span class="badge-latest">TERBARU</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="col-date"><span class="date-txt">{{ $file->date }}</span></td>
                            <td class="col-size"><span class="size-chip">{{ $file->size }}</span></td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.backup.download', ['file' => $file->name]) }}"
                                       class="btn-ico dl" title="Download">
                                        <i class="bi bi-cloud-arrow-down"></i>
                                    </a>
                                    <button class="btn-ico re"
                                            onclick="confirmRestoreExisting('{{ $file->name }}')" title="Restore">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    <button class="btn-ico rm"
                                            onclick="confirmDelete('{{ $file->name }}')" title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <i class="bi bi-info-circle me-1"></i> Sistem hanya menyimpan 10 riwayat backup terakhir. Auto-purge aktif.
            </div>
        @endif
    </div>

    {{-- Bottom Row --}}
    <div class="bottom-row">

        {{-- Restore from local --}}
        <div class="bottom-card">
            <div class="bottom-card-head">
                <div class="head-ico amber"><i class="bi bi-upload" style="color:#854F0B;"></i></div>
                <span style="font-size:13px;font-weight:600;color:#111827;">Restore dari Lokal</span>
            </div>
            <div class="bottom-card-body">
                <p style="font-size:12px;color:#6b7280;margin-bottom:12px;">Upload file <code>.sql</code> dari komputer Anda untuk memulihkan database.</p>
                <form id="restore-upload-form" action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="file-drop" onclick="document.getElementById('backup_file').click()">
                        <i class="bi bi-folder2-open" style="font-size:22px;color:#9ca3af;"></i>
                        <small>Klik untuk pilih file <code>.sql</code></small>
                        <small id="chosen-file-name" style="color:#534AB7;font-weight:600;display:none;"></small>
                    </div>
                    <input type="file" name="backup_file" id="backup_file" accept=".sql" required style="display:none;"
                           onchange="document.getElementById('chosen-file-name').textContent=this.files[0]?.name||'';document.getElementById('chosen-file-name').style.display=this.files[0]?'block':'none';">
                    <button type="button" class="btn-restore-run" onclick="confirmRestoreUpload()">
                        <i class="bi bi-exclamation-triangle-fill"></i> Jalankan Restore
                    </button>
                </form>
            </div>
        </div>

        {{-- System info --}}
        <div class="bottom-card">
            <div class="bottom-card-head">
                <div class="head-ico teal"><i class="bi bi-info-circle" style="color:#0F6E56;"></i></div>
                <span style="font-size:13px;font-weight:600;color:#111827;">Informasi Sistem</span>
            </div>
            <div class="bottom-card-body">
                <ul class="info-list">
                    <li><i class="bi bi-folder2 me-1" style="flex-shrink:0;color:#9ca3af;"></i>
                        <span>Path: <code>storage/app/backups/</code></span></li>
                    <li><i class="bi bi-arrow-repeat me-1" style="flex-shrink:0;color:#9ca3af;"></i>
                        <span>Auto-purge aktif jika lebih dari 10 file</span></li>
                    <li><i class="bi bi-gear me-1" style="flex-shrink:0;color:#9ca3af;"></i>
                        <span>Engine: <strong>Native PHP SQL Dump</strong></span></li>
                    <li><i class="bi bi-shield-check me-1" style="flex-shrink:0;color:#9ca3af;"></i>
                        <span>Hanya admin yang dapat mengakses fitur ini</span></li>
                </ul>
            </div>
        </div>

    </div>
</div>

{{-- Hidden forms --}}
<form id="form-delete" action="{{ route('admin.backup.destroy') }}" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>
<form id="form-restore-existing" action="{{ route('admin.backup.restore-existing') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="filename" id="restore-existing-filename">
</form>

@push('scripts')
<script>
    function confirmBackup() {
        Swal.fire({
            title: 'Buat Backup?',
            text: 'Database akan di-dump dan disimpan ke server.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3C3489',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Backup Sekarang'
        }).then(r => {
            if (r.isConfirmed) {
                Swal.fire({ title: 'Proses backup...', didOpen: () => Swal.showLoading() });
                document.getElementById('form-backup').submit();
            }
        });
    }

    function confirmDelete(filename) {
        Swal.fire({
            title: 'Hapus File?',
            html: `<code style="font-size:12px;">${filename}</code>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Hapus'
        }).then(r => {
            if (r.isConfirmed) {
                const form = document.getElementById('form-delete');
                form.action = '{{ route("admin.backup.destroy") }}?file=' + encodeURIComponent(filename);
                form.submit();
            }
        });
    }

    function confirmRestoreUpload() {
        const fileInput = document.getElementById('backup_file');
        if (!fileInput.files.length) {
            Swal.fire('Oops!', 'Pilih file .sql terlebih dahulu.', 'warning');
            return;
        }
        showRestoreConfirm(() => {
            Swal.fire({ title: 'Sedang restore...', didOpen: () => Swal.showLoading() });
            document.getElementById('restore-upload-form').submit();
        });
    }

    function confirmRestoreExisting(filename) {
        showRestoreConfirm(() => {
            document.getElementById('restore-existing-filename').value = filename;
            Swal.fire({ title: 'Sedang restore...', didOpen: () => Swal.showLoading() });
            document.getElementById('form-restore-existing').submit();
        }, filename);
    }

    function showRestoreConfirm(onConfirm, filename = null) {
        Swal.fire({
            title: '⚠️ Perhatian!',
            html: `Data database saat ini akan <b>dihapus seluruhnya</b> dan digantikan dengan data dari file backup.<br><br>Tindakan ini <b>tidak dapat dibatalkan</b>.`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Timpa Data!',
            input: 'checkbox',
            inputPlaceholder: 'Saya memahami risiko kehilangan data.',
            preConfirm: (value) => {
                if (!value) Swal.showValidationMessage('Centang konfirmasi terlebih dahulu!');
            }
        }).then(r => { if (r.isConfirmed) onConfirm(); });
    }
</script>
@endpush
@endsection