{{-- /resources/views/auth/change-password.blade.php --}}
<x-guest-layout>

    {{-- Sembunyikan logo Laravel bawaan Breeze --}}
    @section('logo')@endsection

    @if(session('warning'))
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;margin-bottom:20px;color:#d97706;font-size:14px;font-weight:600;">
        ⚠️ {{ session('warning') }}
    </div>
    @endif

    <div style="text-align:center;margin-bottom:24px;">
        <div style="font-size:40px;margin-bottom:8px;">🔐</div>
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;">Ganti Password</h2>
        <p style="font-size:13px;color:#94a3b8;margin-top:4px;">
            Anda menggunakan password default. Silakan buat password baru untuk keamanan akun Anda.
        </p>
    </div>

    @if($errors->any())
    <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;margin-bottom:16px;color:#dc2626;font-size:13px;">
        @foreach($errors->all() as $e)
            <p style="margin:0 0 4px;">• {{ $e }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('password.change.update') }}">
        @csrf

        {{-- Password Baru --}}
        <div style="margin-bottom:16px;">
            <x-input-label for="password" value="Password Baru" />
            <div style="position:relative;margin-top:4px;">
                <x-text-input id="password"
                    style="width:100%;padding-right:44px;box-sizing:border-box;"
                    type="password" name="password"
                    required autocomplete="new-password" />
                <button type="button" onclick="togglePass('password','icon-pass')"
                    tabindex="-1"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:0;color:#94a3b8;font-size:16px;line-height:1;">
                    <i class="bi bi-eye-slash" id="icon-pass"></i>
                </button>
            </div>
            <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Minimal 8 karakter</p>

            {{-- Strength indicator --}}
            <div style="margin-top:8px;">
                <div style="height:4px;border-radius:4px;background:#e2e8f0;overflow:hidden;">
                    <div id="strength-bar" style="height:100%;width:0;border-radius:4px;transition:width .3s,background .3s;"></div>
                </div>
                <p id="strength-text" style="font-size:11px;color:#94a3b8;margin-top:4px;"></p>
            </div>
        </div>

        {{-- Konfirmasi Password --}}
        <div style="margin-bottom:20px;">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <div style="position:relative;margin-top:4px;">
                <x-text-input id="password_confirmation"
                    style="width:100%;padding-right:44px;box-sizing:border-box;"
                    type="password" name="password_confirmation"
                    required autocomplete="new-password" />
                <button type="button" onclick="togglePass('password_confirmation','icon-confirm')"
                    tabindex="-1"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:0;color:#94a3b8;font-size:16px;line-height:1;">
                    <i class="bi bi-eye-slash" id="icon-confirm"></i>
                </button>
            </div>
            <p id="match-text" style="font-size:11px;margin-top:4px;"></p>
        </div>

        <x-primary-button class="w-full justify-center">
            🔒 Simpan Password Baru
        </x-primary-button>

        <div style="text-align:center;margin-top:16px;">
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="font-size:13px;color:#94a3b8;background:none;border:none;cursor:pointer;text-decoration:underline;">
                    Logout
                </button>
            </form>
        </div>
    </form>

    <script>
    // Toggle show/hide password
    function togglePass(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon  = document.getElementById(iconId);
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'bi bi-eye';
        } else {
            field.type = 'password';
            icon.className = 'bi bi-eye-slash';
        }
    }

    // Password strength checker
    const passInput    = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const bar          = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    const matchText    = document.getElementById('match-text');

    const levels = [
        { label: 'Sangat Lemah', color: '#ef4444', width: '20%' },
        { label: 'Lemah',        color: '#f97316', width: '40%' },
        { label: 'Cukup',        color: '#eab308', width: '60%' },
        { label: 'Kuat',         color: '#22c55e', width: '80%' },
        { label: 'Sangat Kuat',  color: '#16a34a', width: '100%' },
    ];

    function checkStrength(val) {
        let score = 0;
        if (val.length >= 8)  score++;
        if (val.length >= 12) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        return Math.min(score, 4);
    }

    passInput.addEventListener('input', function () {
        const val = this.value;
        if (!val) {
            bar.style.width = '0';
            strengthText.textContent = '';
            return;
        }
        const lvl = checkStrength(val);
        bar.style.width      = levels[lvl].width;
        bar.style.background = levels[lvl].color;
        strengthText.textContent  = 'Kekuatan: ' + levels[lvl].label;
        strengthText.style.color  = levels[lvl].color;
        checkMatch();
    });

    confirmInput.addEventListener('input', checkMatch);

    function checkMatch() {
        if (!confirmInput.value) { matchText.textContent = ''; return; }
        if (passInput.value === confirmInput.value) {
            matchText.textContent = '✓ Password cocok';
            matchText.style.color = '#16a34a';
        } else {
            matchText.textContent = '✗ Password tidak cocok';
            matchText.style.color = '#ef4444';
        }
    }
    </script>

</x-guest-layout>
