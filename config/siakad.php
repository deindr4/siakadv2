<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reset Password Key
    |--------------------------------------------------------------------------
    | Kunci statis untuk validasi reset password.
    | Set di .env: SIAKAD_RESET_KEY=xxxxxxxx
    | Bagikan kunci ini ke siswa/guru yang perlu reset password.
    |
    */
    'reset_password_key' => env('SIAKAD_RESET_KEY', 'OSAKAJ4Y@2026'),

];
