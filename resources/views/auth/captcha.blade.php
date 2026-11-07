<svg xmlns="http://www.w3.org/2000/svg" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 {{ $width }} {{ $height }}" role="img" aria-label="Soal captcha">
    <rect width="{{ $width }}" height="{{ $height }}" fill="#eef2f7" />

    {{-- Garis gangguan acak agar OCR sulit membaca --}}
    @for ($i = 0; $i < 3; $i++)
        <line
            x1="{{ random_int(0, intval($width / 4)) }}"
            y1="{{ random_int(5, intval($height - 5)) }}"
            x2="{{ random_int(intval($width / 2), $width) }}"
            y2="{{ random_int(5, intval($height - 5)) }}"
            stroke="#9db2cc" stroke-width="1" opacity="0.6" />
    @endfor

    @foreach ($tokens as $token)
        @if ($token['char'] !== ' ')
            <text
                x="{{ $token['x'] }}"
                y="{{ $token['y'] }}"
                font-family="'Trebuchet MS', Verdana, sans-serif"
                font-size="{{ $token['size'] }}"
                font-weight="700"
                fill="#1f3a5f"
                opacity="{{ $token['opacity'] }}"
                transform="rotate({{ $token['rotate'] }} {{ $token['x'] }} {{ $token['y'] }})">{{ $token['char'] }}</text>
        @endif
    @endforeach

    {{-- Titik-titik noise --}}
    @for ($i = 0; $i < 24; $i++)
        <circle cx="{{ random_int(1, intval($width - 1)) }}" cy="{{ random_int(1, intval($height - 1)) }}" r="1" fill="#7f97b5" opacity="0.5" />
    @endfor
</svg>
