{{-- Form Ubah Status Mutasi --}}
@if($siswa->status_mutasi === 'aktif')
<div class="card" style="margin-bottom:24px;border-left:4px solid #6366f1;">
    <div class="card-header"><h3>🔄 Ubah Status Siswa</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.mutasi.store') }}">
            @csrf
            <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;align-items:end;">

                <div>
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">STATUS MUTASI <span style="color:red">*</span></label>
                    <select name="status_mutasi" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Pilih status...</option>
                        <option value="mutasi_masuk">➡️ Mutasi Masuk</option>
                        <option value="mutasi_keluar">⬅️ Mutasi Keluar</option>
                        <option value="putus_sekolah">❌ Putus Sekolah</option>
                        <option value="berhenti">🚫 Berhenti</option>
                        <option value="lulus">🎓 Lulus</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TANGGAL <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_mutasi" required value="{{ date('Y-m-d') }}"
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>

                <div>
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KETERANGAN</label>
                    <input type="text" name="keterangan_mutasi" placeholder="Alasan / keterangan..."
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>

            </div>
            <div style="margin-top:14px;text-align:right;">
                <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Yakin ubah status siswa ini?')"
                    style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                    <i class="bi bi-arrow-left-right"></i> Proses Mutasi
                </button>
            </div>
        </form>
    </div>
</div>
@else
{{-- Jika sudah mutasi, tampilkan tombol kembalikan ke aktif --}}
<div class="card" style="margin-bottom:24px;border-left:4px solid #f59e0b;">
    <div class="card-header"><h3>🔄 Status Mutasi</h3></div>
    <div class="card-body">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <p style="font-size:13px;color:#64748b;">Siswa ini saat ini berstatus <strong>{{ $statusLabel }}</strong></p>
                @if($siswa->tanggal_mutasi)
                <p style="font-size:12px;color:#94a3b8;margin-top:4px;">Sejak: {{ $siswa->tanggal_mutasi->format('d/m/Y') }}</p>
                @endif
                @if($siswa->keterangan_mutasi)
                <p style="font-size:12px;color:#94a3b8;margin-top:2px;">Keterangan: {{ $siswa->keterangan_mutasi }}</p>
                @endif
            </div>
            <form method="POST" action="{{ route('admin.mutasi.restore', $siswa) }}"
                onsubmit="return confirm('Kembalikan siswa ini ke status aktif?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn" style="background:#dcfce7;color:#16a34a;">
                    <i class="bi bi-arrow-counterclockwise"></i> Kembalikan ke Aktif
                </button>
            </form>
        </div>
    </div>
</div>
@endif
