@php
    $icons = ['fa-file-alt', 'fa-clipboard-list', 'fa-id-card', 'fa-stamp', 'fa-folder-open', 'fa-handshake'];
    $icon = $icons[($index ?? 0) % count($icons)];
@endphp
<article class="rsud-service-card">
    <a href="{{ route('public.layanan.show', $layanan->slug) }}" class="rsud-service-card__link">
        <div class="rsud-service-card__icon" aria-hidden="true">
            <i class="fas {{ $icon }}"></i>
        </div>
        <small class="rsud-service-card__code">{{ $layanan->code }}</small>
        <h3 class="rsud-service-card__title">{{ $layanan->title }}</h3>
        <p class="rsud-service-card__desc">
            {{ \Illuminate\Support\Str::limit(strip_tags($layanan->description ?? ''), 100) ?: 'Ajukan layanan secara online.' }}
        </p>
        <span class="rsud-service-card__cta">Ajukan sekarang <i class="fas fa-arrow-right"></i></span>
    </a>
</article>
