<article class="rsud-berita-card">
    <a href="{{ route('public.berita.show', $berita->slug) }}" class="rsud-berita-card__thumb">
        @if ($berita->thumbnailUrl())
            <img src="{{ $berita->thumbnailUrl() }}" alt="{{ $berita->title }}" loading="lazy">
        @else
            <span class="rsud-berita-card__fallback" aria-hidden="true">
                <i class="fas fa-newspaper"></i>
            </span>
        @endif
    </a>
    <div class="rsud-berita-card__body">
        <small class="rsud-berita-card__meta">
            {{ optional($berita->published_at)->translatedFormat('d F Y') ?: '—' }}
            @if ($berita->category)
                · {{ $berita->category->name }}
            @endif
        </small>
        <h4 class="rsud-berita-card__title">
            <a href="{{ route('public.berita.show', $berita->slug) }}">{{ $berita->title }}</a>
        </h4>
        <p class="rsud-berita-card__excerpt">
            {{ $berita->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($berita->content), 110) }}
        </p>
        <a class="rsud-card__link" href="{{ route('public.berita.show', $berita->slug) }}">Baca selengkapnya</a>
    </div>
</article>
