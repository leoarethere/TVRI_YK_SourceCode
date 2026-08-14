@php
    $message = null;
    $type = 'success';

    // Daftar kunci session (Sudah distandarisasi di semua Controller)
    $keys = [
        'success' => 'success',
        'error' => 'error',
        'category_success' => 'success',
        'category_error' => 'error',
    ];

    foreach ($keys as $key => $t) {
        if (session()->has($key)) {
            $message = session($key);
            $type = $t;
            break;
        }
    }

    if (!$message && $errors->any()) {
        $message = 'Terdapat kesalahan validasi. Mohon periksa inputan Anda.';
        $type = 'error';
    }
@endphp

@if($message)
    {{-- PERBAIKAN: ID diganti dari 'flash-message-trigger' menjadi 'notification-trigger' --}}
    {{-- Agar cocok dengan Javascript di dashboard-layout.blade.php --}}
    <div id="notification-trigger"
         data-message="{{ $message }}"
         data-type="{{ $type }}"
         style="display: none;">
    </div>

@endif
