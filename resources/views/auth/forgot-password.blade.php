<!DOCTYPE html>
<html lang="id" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Lupa Kata Sandi' }} - TVRI D.I. Yogyakarta</title>
    
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
            outline: none;
            box-shadow: 0 0 0 2px rgb(37 99 235 / 20%);
            border-color: rgb(37 99 235);
        }
        
        .dark .login-input {
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
            color: rgb(255 255 255);
        }
        
        .dark .login-input:focus {
            box-shadow: 0 0 0 2px rgb(59 130 246 / 20%);
            border-color: rgb(59 130 246);
        }
        
        .login-input-error {
            border-color: rgb(239 68 68);
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
    </style>
</head>
<body class="h-full font-google-sans antialiased text-gray-900 dark:text-white">

    <div class="login-container">
        
        {{-- BAGIAN 1: Banner Image (Tampil Full di Desktop, Sembunyi di Mobile) --}}
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
                        Lupa Kata Sandi?
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Tidak masalah. Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset kata sandi Anda.
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

                {{-- Form --}}
                <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST" novalidate>
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
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-center mb-4">
                        <button type="submit" 
                            class="w-full max-w-md px-8 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800 transition-colors duration-300">
                            Kirim Tautan Reset Kata Sandi
                        </button>
                    </div>

                    {{-- Link Kembali ke Login --}}
                    <div class="flex justify-center">
                        <a href="{{ route('login') }}" 
                        class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                            &larr; Kembali ke halaman Login
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
