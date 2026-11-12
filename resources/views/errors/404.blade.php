@php
    $websiteSettings = \App\Models\WebsiteSetting::current();
    $siteName = $websiteSettings->siteName();
    $logoUrl = $websiteSettings->logoUrl();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Halaman Tidak Ditemukan — {{ $siteName }}</title>
    <link rel="icon" href="{{ $websiteSettings->faviconUrl() ?? asset('optilux/images/icon.webp') }}" type="image/png">
    <link href="{{ asset('optilux/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/public-optilux.css') }}" rel="stylesheet" type="text/css">
    @include('partials.theme-vars')
    <style>
        .kecamatan-error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            background: linear-gradient(135deg, var(--theme-dark) 0%, var(--theme-primary) 42%, var(--theme-light) 100%);
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .kecamatan-error-page::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 85% 20%, rgba(255,255,255,.12) 0%, transparent 45%),
                radial-gradient(circle at 10% 80%, rgba(0,0,0,.15) 0%, transparent 50%);
            pointer-events: none;
        }
        .kecamatan-error-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 560px;
            text-align: center;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 24px 64px rgba(0,0,0,.2);
        }
        .kecamatan-error-card__logo {
            max-height: 64px;
            width: auto;
            max-width: 180px;
            object-fit: contain;
            margin-bottom: .85rem;
        }
        .kecamatan-error-card__fallback {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: rgba(255,255,255,.18);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: .85rem;
        }
        .kecamatan-error-card__site {
            font-size: .85rem;
            font-weight: 600;
            opacity: .85;
            margin-bottom: 1.5rem;
        }
        .kecamatan-error-card__code {
            font-size: clamp(4rem, 12vw, 6rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -.04em;
            margin: 0 0 .5rem;
        }
        .kecamatan-error-card__title {
            font-size: 1.45rem;
            font-weight: 700;
            margin-bottom: .65rem;
        }
        .kecamatan-error-card__desc {
            opacity: .85;
            margin-bottom: 1.75rem;
            line-height: 1.6;
        }
        .kecamatan-error-card__actions {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            justify-content: center;
        }
        .kecamatan-error-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .75rem 1.35rem;
            border-radius: 999px;
            font-weight: 700;
            text-decoration: none;
            transition: transform .15s, box-shadow .15s, background .15s;
        }
        .kecamatan-error-btn--primary {
            background: #fff;
            color: var(--theme-primary);
            box-shadow: 0 8px 24px rgba(0,0,0,.18);
        }
        .kecamatan-error-btn--primary:hover {
            transform: translateY(-1px);
            color: var(--theme-dark);
        }
        .kecamatan-error-btn--ghost {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,.45);
        }
        .kecamatan-error-btn--ghost:hover {
            background: rgba(255,255,255,.1);
            color: #fff;
        }
    </style>
</head>
<body>
    <main class="kecamatan-error-page">
        <div class="kecamatan-error-card">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="kecamatan-error-card__logo">
            @else
                <span class="kecamatan-error-card__fallback">{{ \Illuminate\Support\Str::substr($siteName, 0, 1) }}</span>
            @endif
            <div class="kecamatan-error-card__site">{{ $siteName }}</div>
            <p class="kecamatan-error-card__code">404</p>
            <h1 class="kecamatan-error-card__title">Halaman tidak ditemukan</h1>
            <p class="kecamatan-error-card__desc">
                Maaf, halaman yang Anda cari tidak tersedia atau sudah dipindahkan.
                Silakan kembali ke beranda atau buka layanan online.
            </p>
            <div class="kecamatan-error-card__actions">
                <a href="{{ route('home') }}" class="kecamatan-error-btn kecamatan-error-btn--primary">Kembali ke Beranda</a>
                <a href="{{ route('public.layanan.index') }}" class="kecamatan-error-btn kecamatan-error-btn--ghost">Layanan Online</a>
            </div>
        </div>
    </main>
</body>
</html>
