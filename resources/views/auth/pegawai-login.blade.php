<!DOCTYPE html>
<html lang="id" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Portal Pegawai' }} - TVRI D.I. Yogyakarta</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Google Sans Flex Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet">
    
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png">
    
    @include('components.auth-styles')
</head>
<body class="h-full font-google-sans antialiased text-gray-900 dark:text-white">

    <div class="login-container">
        
        {{-- BAGIAN 1: Banner Image --}}
        <div class="login-banner relative h-full w-full bg-gray-100 dark:bg-gray-800">
            <img src="{{ asset('img/loginbanner.jpeg') }}" 
                 alt="Banner Login TVRI D.I. Yogyakarta" 
                 class="absolute inset-0 h-full w-full object-cover"
                 loading="lazy" />
            
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 via-indigo-900/30 to-transparent"></div>
        </div>

        {{-- BAGIAN 2: Form Login --}}
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
                
                {{-- Header Text --}}
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Portal Pegawai
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Silahkan login untuk mengakses layanan internal pegawai.
                    </p>
                </div>

                {{-- Notifikasi Error --}}
                @if ($errors->any())
                    <div class="alert-container alert-error" role="alert">
                        <div class="flex items-start">
                            <svg class="flex-shrink-0 w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <span class="font-medium">Gagal Masuk!</span>
                                <ul class="mt-1 list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Notifikasi Sukses --}}
                @if (session('success'))
                    <div class="alert-container alert-success" role="alert">
                        <div class="flex items-start">
                            <svg class="flex-shrink-0 w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Notifikasi Info --}}
                @if (session('status'))
                    <div class="alert-container alert-info" role="alert">
                        <div class="flex items-start">
                            <svg class="flex-shrink-0 w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Form --}}
                <form class="mt-8 space-y-6" action="{{ route('pegawai.login') }}" method="POST" novalidate data-turbo="false">
                    @csrf

                    <div class="space-y-4">
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Alamat email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="login-input @error('email') login-input-error @enderror" 
                                placeholder="nama@email.com" 
                                required 
                                autofocus
                                autocomplete="email">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Kata sandi <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" 
                                placeholder="••••••••" 
                                class="login-input @error('password') login-input-error @enderror" 
                                required
                                autocomplete="current-password">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Remember Me (No Forgot Password untuk pegawai) --}}
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                            class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800">
                        <label for="remember" class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-300">
                            Ingat saya
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-center mb-4">
                        <button type="submit" 
                            class="w-full max-w-md px-8 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800 transition-colors duration-300">
                            Masuk sebagai Pegawai
                        </button>
                    </div>

                    {{-- Divider & Tombol Login ke Dashboard --}}
                    <div class="relative my-2">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="bg-white dark:bg-gray-900 px-3 text-gray-400 dark:text-gray-500">atau</span>
                        </div>
                    </div>

                    <div class="flex justify-center mb-2">
                        <a href="{{ route('login') }}"
                           class="w-full max-w-md flex items-center justify-center gap-2 px-8 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Login ke Dashboard
                        </a>
                    </div>

                    {{-- Link Kembali ke Homepage --}}
                    <div class="flex justify-start">
                        <a href="{{ route('home') }}" 
                        class="px-4 py-2 text-sm font-medium text-blue-600 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-blue-400 dark:hover:bg-gray-600 transition-colors">
                            Kembali ke Beranda
                        </a>
                    </div>
                </form>

                {{-- Footer --}}
                <div class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
                    <p>Copyright {{ date('Y') }} Lembaga Penyiaran Publik TVRI D.I. Yogyakarta</p>
                    <p class="mt-1">Hak cipta dilindungi undang-undang.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
