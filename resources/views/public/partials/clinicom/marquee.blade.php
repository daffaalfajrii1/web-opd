@php
    $marqueeItems = $websiteSettings->marqueeItems();
    if (count($marqueeItems) === 0 && \Illuminate\Support\Facades\Schema::hasTable('pengumumans')) {
        $marqueeItems = \App\Models\Pengumuman::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(8)
            ->pluck('title')
            ->filter()
            ->values()
            ->all();
    }
@endphp

@if (count($marqueeItems) > 0)
    <div class="rsud-marquee" aria-label="Pengumuman berjalan">
        <div class="container">
            <div class="rsud-marquee__inner">
                <span class="rsud-marquee__label"><i class="fas fa-bullhorn"></i> Pengumuman</span>
                <div class="rsud-marquee__viewport">
                    <div class="rsud-marquee__track">
                        @foreach (array_merge($marqueeItems, $marqueeItems) as $item)
                            <span class="rsud-marquee__item">{{ $item }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
