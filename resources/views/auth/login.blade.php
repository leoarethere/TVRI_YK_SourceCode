<!DOCTYPE html>
<html lang="id" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - TVRI D.I. Yogyakarta</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Google Sans Flex Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet">
    
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png">
    
    <style>
        body {
            font-family: "Google Sans Flex", sans-serif;
        }
        
        /* Fix CSS Conflicts */
        .login-container {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr;
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
            .login-form-section {
                padding: 3rem;
            }
        }
        
        @media (min-width: 1024px) {
            .login-form-section {
                padding: 4rem;
            }
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
            .login-logo {
                height: 3rem;
            }
        }
        
        .login-input {
            background-color: rgb(249 250 251);
            border: 1px solid rgb(209 213 219);
            color: rgb(17 24 39);
            font-size: 0.875rem;
            border-radius: 0.5rem;
            padding: 0.625rem;
            width: 100%;
        }
        
        .login-input:focus {
            ring-width: 2px;
            ring-color: rgb(37 99 235);
            border-color: rgb(37 99 235);
        }
        
        .dark .login-input {
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
            color: rgb(255 255 255);
        }
        
        .dark .login-input:focus {
            ring-color: rgb(59 130 246);
            border-color: rgb(59 130 246);
        }
        
        .login-input-error {
            border-color: rgb(239 68 68);
        }
        
        .login-button {
            width: 100%;
            color: rgb(255 255 255);
            background-color: rgb(37 99 235);
            font-weight: 500;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            padding: 0.625rem 1.25rem;
            text-align: center;
        }
        
        .login-button:hover {
            background-color: rgb(29 78 216);
        }
        
        .dark .login-button {
            background-color: rgb(37 99 235);
        }
        
        .dark .login-button:hover {
            background-color: rgb(29 78 216);
        }
        
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
        
        .alert-info {
            color: rgb(30 64 175);
            background-color: rgb(239 246 255);
            border-color: rgb(191 219 254);
        }
        
        .dark .alert-info {
            color: rgb(191 219 254);
            background-color: rgb(30 58 138);
            border-color: rgb(30 64 175);
        }
    </style>
</head>
<body class="h-full font-google-sans antialiased text-gray-900 dark:text-white">

    <div class="login-container">
        
        {{-- BAGIAN 1: Banner Image (Tampil Full di Desktop, Sembunyi di Mobile) --}}
        <div class="login-banner relative h-full w-full bg-gray-100 dark:bg-gray-800">
            <img src="{{ asset('img/loginbanner.jpeg') }}" 
                 alt="TVRI D.I. Yogyakarta Login Banner" 
                 class="absolute inset-0 h-full w-full object-cover" />
            
            {{-- Overlay Gradient --}}
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 via-indigo-900/30 to-transparent"></div>
        </div>

        {{-- BAGIAN 2: Form Login --}}
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
                        Selamat Datang Kembali!
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Silahkan lakukan login terlebih dahulu untuk mengakses dashboard.
                    </p>
                </div>

                {{-- Notifikasi Error --}}
                @if ($errors->any())
                    <div class="alert-container alert-error">
                        <div class="flex items-start">
                            <svg class="flex-shrink-0 w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <span class="font-medium">Login Gagal!</span>
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
                    <div class="alert-container alert-success">
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
                    <div class="alert-container alert-info">
                        <div class="flex items-start">
                            <svg class="flex-shrink-0 w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Form --}}
                <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST" novalidate>
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

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800">
                            <label for="remember" class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-300">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-center mb-4">
                        <button type="submit" 
                            class="w-full max-w-md px-8 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800 transition-colors duration-300">
                            Masuk
                        </button>
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
                    <p class="mt-1">All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>