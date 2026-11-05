<x-auth-layout
    :title="$title ?? 'Portal Pegawai'"
    heading="Portal Pegawai"
    subheading="Masuk untuk mengakses layanan internal pegawai."
    error-title="Gagal masuk"
>
    <form class="auth-form" action="{{ route('pegawai.login') }}" method="POST" novalidate data-turbo="false">
        @csrf

        <div class="auth-field">
            <label for="email" class="auth-label">Alamat email <span class="auth-req">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="auth-input @error('email') is-invalid @enderror"
                   placeholder="nama@email.com"
                   required autofocus autocomplete="email">
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password" class="auth-label">Kata sandi <span class="auth-req">*</span></label>
            <input type="password" name="password" id="password"
                   class="auth-input @error('password') is-invalid @enderror"
                   placeholder="••••••••"
                   required autocomplete="current-password">
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-row">
            <label class="auth-check" for="remember">
                <input id="remember" name="remember" type="checkbox">
                Ingat saya
            </label>
            <a href="{{ route('password.request', ['portal' => 'pegawai']) }}" class="auth-link">
                Lupa kata sandi?
            </a>
        </div>

        <button type="submit" class="auth-btn auth-btn-primary" data-loading-text="Memproses...">
            Masuk sebagai pegawai
        </button>

        <div class="auth-divider">atau</div>

        <a href="{{ route('login') }}" class="auth-btn auth-btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Login ke dashboard
        </a>

        <a href="{{ route('home') }}" class="auth-back">&larr; Kembali ke beranda</a>
    </form>
</x-auth-layout>