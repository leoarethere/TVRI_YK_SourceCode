@props([
    'title' => 'Login',
    'heading' => 'Selamat datang kembali',
    'subheading' => '',
    'errorTitle' => 'Login gagal',
])

<!DOCTYPE html>
<html lang="id" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - TVRI D.I. Yogyakarta</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Sans Flex Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet">

    <x-favicon />

    <style>
        body {
            font-family: "Google Sans Flex", sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .login-container {
                grid-template-columns: 1fr 1fr;
            }
        }

        .login-banner {
            display: none;
        }

        @media (min-width: 1024px) {
            .login-banner {
                display: block;
            }
        }

        .login-form-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        @media (min-width: 640px) {
            .login-form-section { padding: 3rem; }
        }

        @media (min-width: 1024px) {
            .login-form-section { padding: 4rem; }
        }

        .login-form-container {
            width: 100%;
            max-width: 28rem;
            margin: 0 auto;
        }

        .login-logo {
            height: 2.25rem;
            width: auto;
        }

        @media (min-width: 768px) {
            .login-logo { height: 3rem; }
        }

        /* --- Kelas form auth yang dipakai semua halaman login --- */
        .auth-form { display: grid; gap: 1.25rem; }

        .auth-field { display: grid; gap: 0.375rem; }

        .auth-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: rgb(31 41 55);
        }

        .dark .auth-label { color: rgb(255 255 255); }

        .auth-req { color: rgb(239 68 68); }

        .auth-input {
            background-color: rgb(249 250 251);
            border: 1px solid rgb(209 213 219);
            color: rgb(17 24 39);
            font-size: 0.875rem;
            border-radius: 0.5rem;
            padding: 0.625rem;
            width: 100%;
        }

        .auth-input::placeholder { color: rgb(156 163 175); }

        .auth-input:focus {
            outline: 2px solid rgb(37 99 235);
            outline-offset: 2px;
            border-color: rgb(37 99 235);
        }

        .dark .auth-input {
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
            color: rgb(255 255 255);
        }

        .dark .auth-input:focus { outline-color: rgb(59 130 246); }

        .auth-input.is-invalid { border-color: rgb(239 68 68); }

        .auth-error {
            margin-top: 0.25rem;
            font-size: 0.8125rem;
            color: rgb(220 38 38);
        }

        .dark .auth-error { color: rgb(248 113 113); }

        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .auth-check {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: rgb(75 85 99);
            cursor: pointer;
        }

        .dark .auth-check { color: rgb(209 213 219); }

        .auth-link {
            font-size: 0.875rem;
            font-weight: 500;
            color: rgb(37 99 235);
        }

        .auth-link:hover { color: rgb(29 78 216); text-decoration: underline; }

        .dark .auth-link { color: rgb(96 165 250); }

        .auth-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            text-align: center;
            cursor: pointer;
        }

        .auth-btn svg { width: 1.25rem; height: 1.25rem; }

        .auth-btn-primary {
            color: rgb(255 255 255);
            background-color: rgb(37 99 235);
        }

        .auth-btn-primary:hover {
            background-color: rgb(29 78 216);
        }

        .dark .auth-btn-primary { background-color: rgb(37 99 235); }
        .dark .auth-btn-primary:hover { background-color: rgb(29 78 216); }

        .auth-btn-secondary {
            color: rgb(37 99 235);
            background-color: rgb(243 244 246);
            border: 1px solid rgb(229 231 235);
        }

        .auth-btn-secondary:hover { background-color: rgb(229 231 235); }

        .dark .auth-btn-secondary {
            color: rgb(96 165 250);
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
        }

        .dark .auth-btn-secondary:hover { background-color: rgb(75 85 99); }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.75rem;
            color: rgb(156 163 175);
        }

        .auth-divider::before,
        .auth-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgb(229 231 235);
        }

        .dark .auth-divider::before,
        .dark .auth-divider::after { background: rgb(75 85 99); }

        .auth-back {
            display: inline-block;
            font-size: 0.875rem;
            color: rgb(107 114 128);
        }

        .auth-back:hover { text-decoration: underline; }

        .dark .auth-back { color: rgb(156 163 175); }

        .auth-failed {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8125rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            color: rgb(153 27 27);
            background-color: rgb(254 226 226);
        }

        .dark .auth-failed {
            color: rgb(254 202 202);
            background-color: rgb(88 28 28);
        }

        .auth-success-pill {
            font-size: 0.8125rem;
            color: rgb(21 128 61);
            background-color: rgb(220 252 231);
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }

        .dark .auth-success-pill {
            color: rgb(187 247 208);
            background-color: rgb(20 83 45);
        }

        /* Captcha */
        .auth-captcha {
            display: grid;
            gap: 0.375rem;
        }

        .auth-captcha-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            background-color: rgb(249 250 251);
            border: 1px solid rgb(209 213 219);
            border-radius: 0.5rem;
            padding: 0.375rem 0.5rem;
        }

        .dark .auth-captcha-box {
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
        }

        .auth-captcha-image {
            display: block;
            cursor: pointer;
            border-radius: 0.375rem;
        }

        .auth-captcha-refresh {
            font-size: 0.75rem;
            color: rgb(75 85 99);
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }

        .dark .auth-captcha-refresh { color: rgb(156 163 175); }

        .alert-container {
            padding: 1rem;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            border: 1px solid;
        }

        .alert-error {
            color: rgb(153 27 27);
            background-color: rgb(254 242 242);
            border-color: rgb(254 202 202);
        }

        .dark .alert-error {
            color: rgb(254 202 202);
            background-color: rgb(127 29 29);
            border-color: rgb(153 27 27);
        }

        .alert-success {
            color: rgb(21 128 61);
            background-color: rgb(240 253 244);
            border-color: rgb(187 247 208);
        }

        .dark .alert-success {
            color: rgb(187 247 208);
            background-color: rgb(22 101 52);
            border-color: rgb(21 128 61);
        }
    </style>
</head>
<body class="h-full antialiased text-gray-900 dark:text-white">

    <div class="login-container">

        {{-- Banner image (full di desktop, sembunyi di mobile) --}}
        <div class="login-banner relative h-full w-full bg-gray-100 dark:bg-gray-800">
            <img src="{{ asset('img/loginbanner.jpeg') }}"
                 alt="TVRI D.I. Yogyakarta Login Banner"
                 class="absolute inset-0 h-full w-full object-cover" />

            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 via-indigo-900/30 to-transparent"></div>
        </div>

        {{-- Form --}}
        <div class="login-form-section bg-white dark:bg-gray-900">
            <div class="login-form-container space-y-8">
                {{-- Logo --}}
                <div class="items-center justify-center flex">
                    <a href="/" class="flex items-center gap-2">
                        <img src="{{ asset('img/logolight.png') }}"
                             class="login-logo block dark:hidden"
                             alt="TVRI D.I. Yogyakarta Logo" />

                        <img src="{{ asset('img/logodark.png') }}"
                             class="login-logo hidden dark:block"
                             alt="TVRI D.I. Yogyakarta Logo" />

                        <span class="text-gray-900 dark:text-white font-bold text-xl sm:text-2xl whitespace-nowrap">
                            Yogyakarta
                        </span>
                    </a>
                </div>

                {{-- Header --}}
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $heading }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $subheading }}
                    </p>
                </div>

                {{-- Notifikasi error --}}
                @if ($errors->any())
                    <div class="alert-container alert-error">
                        <div class="flex items-start">
                            <svg class="flex-shrink-0 w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <span class="font-medium">{{ $errorTitle }}!</span>
                                <ul class="mt-1 list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Notifikasi sukses --}}
                @if (session('success'))
                    <div class="alert-container alert-success">
                        <div class="flex items-start">
                            <svg class="flex-shrink-0 w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <div class="grid gap-4">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <div class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
                    <p>Copyright {{ date('Y') }} Lembaga Penyiaran Publik TVRI D.I. Yogyakarta</p>
                    <p class="mt-1">All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
