<x-auth-layout
    :title="$title ?? 'Login'"
    heading="Selamat datang kembali"
    subheading="Masuk terlebih dahulu untuk mengakses dashboard."
    error-title="Login gagal"
>
    <form class="auth-form" action="{{ route('login') }}" method="POST" novalidate>
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
        </div>

        <button type="submit" class="auth-btn auth-btn-primary">Masuk</button>

        <a href="{{ route('home') }}" class="auth-back">&larr; Kembali ke beranda</a>
    </form>
</x-auth-layout>