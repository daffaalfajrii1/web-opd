<article class="rsud-news-card">
    <a href="{{ route('public.berita.show', $berita->slug) }}" class="rsud-news-card__media">
        @if ($berita->thumbnailUrl())
            <img src="{{ $berita->thumbnailUrl() }}" alt="{{ $berita->title }}" loading="lazy">
        @else
            <span class="rsud-news-card__placeholder" aria-hidden="true"><i class="fas fa-newspaper"></i></span>
        @endif
        @if ($berita->category)
            <span class="rsud-news-card__badge">{{ $berita->category->name }}</span>
        @endif
    </a>
    <div class="rsud-news-card__body">
        <div class="rsud-news-card__meta">
            <i class="far fa-clock"></i>
            {{ optional($berita->published_at)->translatedFormat('d M Y') ?: '—' }}
        </div>
        <h3 class="rsud-news-card__title">
            <a href="{{ route('public.berita.show', $berita->slug) }}">{{ \Illuminate\Support\Str::limit($berita->title, 72) }}</a>
        </h3>
    </div>
</article>
