<!DOCTYPE html>
<html lang="id" class="{{ session('theme', 'light') }} h-full bg-gray-800 scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ============================================== --}}
    {{-- SEO CORE META TAGS                             --}}
    {{-- ============================================== --}}
    <meta name="description" content="{{ $meta_description ?? 'Portal Resmi TVRI Stasiun D.I. Yogyakarta - Lembaga Penyiaran Publik, Media Pemersatu Bangsa. Berita, Program Siaran, dan Layanan Publik.' }}">
    <meta name="keywords" content="TVRI Yogyakarta, TVRI DIY, TVRI Stasiun Yogyakarta, Lembaga Penyiaran Publik, Berita Yogyakarta, Program TVRI, Siaran TVRI Jogja, Media Pemersatu Bangsa">
    <meta name="author" content="TVRI Stasiun D.I. Yogyakarta">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

    {{-- Canonical URL (Mencegah penalti konten duplikat dari Google) --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- ============================================== --}}
    {{-- GEO META TAGS (Local SEO)                      --}}
    {{-- Koordinat: TVRI Yogyakarta, Jl. Magelang KM 4.5 --}}
    {{-- ============================================== --}}
    <meta name="geo.region" content="ID-YO">
    <meta name="geo.placename" content="Yogyakarta, D.I. Yogyakarta, Indonesia">
    <meta name="geo.position" content="-7.7554382;110.3613585">
    <meta name="ICBM" content="-7.7554382, 110.3613585">
    <meta name="language" content="id">
    <meta name="content-language" content="id-ID">

    {{-- ============================================== --}}
    {{-- OPEN GRAPH META (Facebook, WhatsApp, LinkedIn) --}}
    {{-- ============================================== --}}
    <meta property="og:site_name" content="TVRI Stasiun D.I. Yogyakarta">
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="{{ $og_type ?? 'website' }}">
    <meta property="og:title" content="{{ $title ?? 'TVRI D.I. Yogyakarta' }}">
    <meta property="og:description" content="{{ $meta_description ?? 'Lembaga Penyiaran Publik TVRI Stasiun D.I. Yogyakarta - Media Pemersatu Bangsa.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $og_image ?? asset('img/og-default.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $title ?? 'TVRI D.I. Yogyakarta' }}">

    {{-- ============================================== --}}
    {{-- TWITTER CARD META                              --}}
    {{-- ============================================== --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'TVRI D.I. Yogyakarta' }}">
    <meta name="twitter:description" content="{{ $meta_description ?? 'Lembaga Penyiaran Publik TVRI Stasiun D.I. Yogyakarta.' }}">
    <meta name="twitter:image" content="{{ $og_image ?? asset('img/og-default.png') }}">

    @stack('styles')

    <title>{{ $title ?? config('app.name', 'TVRI D.I. Yogyakarta') }}</title>

    {{-- ============================================== --}}
    {{-- JSON-LD STRUCTURED DATA (Organization)         --}}
    {{-- Membantu Google menampilkan Knowledge Panel     --}}
    {{-- ============================================== --}}
    @php
        $jsonLdOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BroadcastService',
            'name' => 'TVRI Stasiun D.I. Yogyakarta',
            'alternateName' => 'TVRI Jogja',
            'description' => 'Lembaga Penyiaran Publik TVRI Stasiun Daerah Istimewa Yogyakarta - Media Pemersatu Bangsa',
            'url' => config('app.url'),
            'logo' => asset('img/favicon.png'),
            'broadcastDisplayName' => 'TVRI D.I. Yogyakarta',
            'broadcastTimezone' => 'Asia/Jakarta',
            'parentService' => [
                '@type' => 'BroadcastService',
                'name' => 'Televisi Republik Indonesia (TVRI)',
                'url' => 'https://www.tvri.go.id',
            ],
            'broadcaster' => [
                '@type' => 'Organization',
                'name' => 'TVRI Stasiun D.I. Yogyakarta',
                'url' => config('app.url'),
                'logo' => asset('img/favicon.png'),
                'sameAs' => array_values(array_filter([
                    ($socialMedia ?? null)?->instagram,
                    ($socialMedia ?? null)?->youtube,
                    ($socialMedia ?? null)?->facebook,
                    ($socialMedia ?? null)?->twitter,
                    ($socialMedia ?? null)?->tiktok,
                ])),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Jl. Magelang KM 4.5',
                    'addressLocality' => 'Yogyakarta',
                    'addressRegion' => 'D.I. Yogyakarta',
                    'postalCode' => '55284',
                    'addressCountry' => 'ID',
                ],
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => '-7.7554382',
                    'longitude' => '110.3613585',
                ],
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer service',
                    'availableLanguage' => 'Indonesian',
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($organizationSchema, $jsonLdOptions) !!}</script>
    <style>
        html, body {
            overscroll-behavior: none;
        }
        body {
            font-family: "Google Sans Flex", sans-serif;
            overflow-x: hidden;
            overscroll-behavior-y: contain;
        }
    </style>
    @vite([
        'resources/css/app.css', 
        'resources/js/app.js', 
        'resources/js/dashboard.js'
    ])

    {{-- 👇 TEMPELKAN KODE GOOGLE FONTS BARU DI SINI --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet">

    {{-- PERBAIKAN FAVICON --}}
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}" type="image/png">
</head>

{{-- PERBAIKAN 1: Hapus kelas yang tidak perlu dari body --}}
<body class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 antialiased">
    {{-- Struktur flex untuk sticky footer --}}
    <div class="flex flex-col min-h-screen">
        <x-navbar/>
        {{-- PERBAIKAN 2: Tambahkan 'pt-16' untuk memberi ruang bagi navbar --}}
        <main class="flex-grow pt-24">
            {{-- PERBAIKAN 3: Gunakan kontainer yang konsisten dengan halaman lain --}}
            {{-- Padding vertikal (py-8) ditambahkan di sini untuk jarak atas/bawah konten --}}
            <div class="container px-0 sm:px-0 lg:px-0 py-11 lg:py-11 relative mx-auto">
                {{ $slot }}
            </div>
        </main>
        <x-footer/>
    </div>
    @stack('scripts')
    {{-- Sienna Accessibility Widget --}}
    <script src="https://cdn.jsdelivr.net/npm/sienna-accessibility/dist/sienna-accessibility.umd.js" defer></script>
</body>
</html>
