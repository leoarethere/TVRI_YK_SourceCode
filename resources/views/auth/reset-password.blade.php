<!DOCTYPE html>
<html lang="id" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Reset Kata Sandi' }} - TVRI D.I. Yogyakarta</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Google Sans Flex Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet">
    
    <x-favicon />
    
    @include('components.auth-styles')
</head>
<body class="h-full font-google-sans antialiased text-gray-900 dark:text-white">

    <div class="login-container">
        
        {{-- BAGIAN 1: Banner Image --}}
        <div class="login-banner relative h-full w-full bg-gray-100 dark:bg-gray-800">
            <img src="{{ asset('img/loginbanner.jpeg') }}" 
                 alt="Banner Login TVRI D.I. Yogyakarta" 
                 class="absolute inset-0 h-full w-full object-cover" />
            
            {{-- Overlay Gradient --}}
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 via-indigo-900/30 to-transparent"></div>
        </div>

        {{-- BAGIAN 2: Form --}}
        <div class="login-form-section bg-white dark:bg-gray-900">
            <div class="login-form-container space-y-8">
                {{-- Logo --}}
                <div class="items-center justify-center flex">
                    <a href="/" class="flex items-center gap-2">
                        {{-- Logo Light Mode --}}
                        <img src="{{ asset('img/logolight.png') }}" 
                            class="login-logo block dark:hidden" 
                            alt="TVRI D.I. Yogyakarta Logo" />

                        {{-- Logo Dark Mode --}}
                        <img src="{{ asset('img/logodark.png') }}" 
                            class="login-logo hidden dark:block" 
                            alt="TVRI D.I. Yogyakarta Logo" />

                        <span class="text-gray-900 dark:text-white font-bold text-xl sm:text-2xl whitespace-nowrap">
                            Yogyakarta
                        </span>
                    </a>
                </div>
                
                {{-- Header Text --}}
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Reset Kata Sandi
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Silakan buat kata sandi baru untuk akun Anda.
                    </p>
                </div>

                {{-- Notifikasi Error --}}
                @if ($errors->any())
                    <div id="form-errors" class="alert-container alert-error" role="alert" aria-live="assertive">
                        <div class="flex items-start">
                            <svg class="flex-shrink-0 w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <span class="font-medium">Terjadi Kesalahan!</span>
                                <ul class="mt-1 list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Form --}}
                <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST" novalidate>
                    @csrf
                    
                    {{-- Token (Hidden) --}}
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="space-y-4">
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Alamat email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}"
                                class="login-input @error('email') login-input-error @enderror" 
                                placeholder="nama@email.com" 
                                required 
                                autofocus
                                autocomplete="email"
                                inputmode="email"
                                @error('email') aria-invalid="true" aria-describedby="form-errors" @enderror>
                        </div>

                        {{-- Password Baru --}}
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" 
                                placeholder="••••••••" 
                                class="login-input @error('password') login-input-error @enderror" 
                                required
                                minlength="8"
                                autocomplete="new-password"
                                aria-describedby="password-requirements @error('password') form-errors @enderror"
                                @error('password') aria-invalid="true" @enderror>
                            <p id="password-requirements" class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Minimal 8 karakter dengan huruf besar, huruf kecil, angka, dan simbol.
                            </p>
                        </div>
                        
                        {{-- Konfirmasi Password Baru --}}
                        <div>
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                placeholder="••••••••" 
                                class="login-input" 
                                required
                                minlength="8"
                                autocomplete="new-password"
                                @error('password') aria-invalid="true" aria-describedby="form-errors" @enderror>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-center mt-6">
                        <button type="submit" data-loading-text="Menyimpan..."
                            class="min-h-11 w-full max-w-md px-8 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800 transition-colors duration-300">
                            Reset Kata Sandi
                        </button>
                    </div>

                    <div class="flex justify-center">
                        <a href="{{ $request->query('portal') === 'pegawai' ? route('pegawai.login') : route('login') }}"
                            class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                            &larr; Kembali ke {{ $request->query('portal') === 'pegawai' ? 'Portal Pegawai' : 'halaman Login' }}
                        </a>
                    </div>
                </form>

                {{-- Footer --}}
                <div class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
                    <p>&copy; {{ date('Y') }} Lembaga Penyiaran Publik TVRI D.I. Yogyakarta</p>
                    <p class="mt-1">Hak cipta dilindungi undang-undang.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
