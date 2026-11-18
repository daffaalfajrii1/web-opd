@php
    /** @var \App\Models\Pengumuman $pengumuman */
@endphp
<article class="rsud-announce-card {{ $pengumuman->isPenting() ? 'rsud-announce-card--penting' : '' }}">
    <div class="rsud-announce-card__top">
        <div class="rsud-announce-card__icon" aria-hidden="true">
            <i class="fas fa-bullhorn"></i>
        </div>
        <div class="rsud-announce-card__badges">
            @if ($pengumuman->isPenting())
                <span class="rsud-badge rsud-badge--danger">Penting</span>
            @endif
            @if ($pengumuman->is_featured)
                <span class="rsud-badge rsud-badge--accent">Unggulan</span>
            @endif
        </div>
    </div>

    <div class="rsud-announce-card__body">
        <time class="rsud-announce-card__date" datetime="{{ optional($pengumuman->published_at)->toDateString() }}">
            <i class="far fa-calendar-alt"></i>
            {{ optional($pengumuman->published_at)->translatedFormat('d F Y') ?: '—' }}
        </time>
        <h3 class="rsud-announce-card__title">
            <a href="{{ route('public.pengumuman.show', $pengumuman->slug) }}">{{ $pengumuman->title }}</a>
        </h3>
        <p class="rsud-announce-card__excerpt">
            {{ \Illuminate\Support\Str::limit(strip_tags($pengumuman->excerpt ?: $pengumuman->content), 110) }}
        </p>
    </div>

    <div class="rsud-announce-card__foot">
        <span class="rsud-announce-card__attach">
            <i class="fas fa-paperclip"></i>
            {{ $pengumuman->attachmentsCountLabel() }}
        </span>
        <a class="rsud-announce-card__link" href="{{ route('public.pengumuman.show', $pengumuman->slug) }}">
            Baca detail <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</article>
