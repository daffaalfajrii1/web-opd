@extends('public.layouts.clinicom')

@section('title', $agenda->title)
@section('meta_description', $agenda->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $agenda->content), 160))
@section('meta_type', 'article')
@section('meta_url', route('public.agenda.show', $agenda->slug))
@section('meta_image', $agenda->thumbnailUrl() ?: $websiteSettings->defaultShareImageUrl())

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => $agenda->title,
        'breadcrumbs' => [
            ['label' => 'Agenda', 'url' => route('public.agenda.index')],
            ['label' => $agenda->title],
        ],
    ])

    <section>
        <div class="container">
            <div class="row gx-5">
                <div class="col-lg-8">
                    @if ($agenda->thumbnailUrl())
                        <img src="{{ $agenda->thumbnailUrl() }}" class="w-100 rounded-1 mb-4" alt="{{ $agenda->title }}">
                    @endif

                    <div class="bg-light p-4 rounded-1 mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong>Tanggal</strong><br>{{ $agenda->formattedEventDate() }}
                            </div>
                            @if ($agenda->formattedTimeRange())
                                <div class="col-md-6">
                                    <strong>Waktu</strong><br>{{ $agenda->formattedTimeRange() }}
                                </div>
                            @endif
                            <div class="col-md-6">
                                <strong>Lokasi</strong><br>{{ $agenda->locationDisplay() }}
                            </div>
                            <div class="col-md-6">
                                <strong>Jenis</strong><br>{{ $agenda->eventTypeLabel() }}
                            </div>
                            @if ($agenda->organizer)
                                <div class="col-md-6">
                                    <strong>Penyelenggara</strong><br>{{ $agenda->organizer }}
                                </div>
                            @endif
                            @if ($agenda->contact_name || $agenda->contact_phone)
                                <div class="col-md-6">
                                    <strong>Kontak</strong><br>
                                    {{ $agenda->contact_name }}
                                    @if ($agenda->contact_phone)
                                        — {{ $agenda->contact_phone }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="kecamatan-content">
                        {!! $agenda->content !!}
                    </div>

                    <div class="mt-4 d-flex flex-wrap gap-2">
                        @if ($agenda->registration_url)
                            <a href="{{ $agenda->registration_url }}" class="btn-main fx-slide" target="_blank" rel="noopener"><span>Daftar Kegiatan</span></a>
                        @endif
                        @if ($agenda->is_online && $agenda->online_link)
                            <a href="{{ $agenda->online_link }}" class="btn-main btn-line fx-slide" target="_blank" rel="noopener"><span>Link Online</span></a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    @if ($related->isNotEmpty())
                        <h4 class="mb-3">Agenda Lainnya</h4>
                        @foreach ($related as $item)
                            <div class="mb-3">
                                <a href="{{ route('public.agenda.show', $item->slug) }}" class="text-dark fw-bold">{{ $item->title }}</a>
                                <div class="small text-muted">{{ $item->formattedEventDate() }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
