@extends('public.layouts.clinicom')

@section('title', 'Agenda')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Agenda Kegiatan',
        'breadcrumbs' => [['label' => 'Agenda']],
    ])

    <section>
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('public.agenda.index', ['filter' => 'upcoming']) }}" class="btn btn-sm {{ $filter === 'upcoming' ? 'btn-main' : 'btn-light' }} me-2">Mendatang</a>
                <a href="{{ route('public.agenda.index', ['filter' => 'past']) }}" class="btn btn-sm {{ $filter === 'past' ? 'btn-main' : 'btn-light' }}">Selesai</a>
            </div>

            <div class="row g-4">
                @forelse ($agendas as $agenda)
                    <div class="col-lg-4 col-md-6">
                        <div class="bg-white p-4 rounded-1 h-100 shadow-soft hover">
                            @if ($agenda->thumbnailUrl())
                                <img src="{{ $agenda->thumbnailUrl() }}" alt="{{ $agenda->title }}" class="w-100 rounded-1 mb-3 placeholder-thumb">
                            @endif
                            <div class="fw-bold id-color mb-2">{{ $agenda->formattedEventDate() }}</div>
                            @if ($agenda->formattedTimeRange())
                                <div class="small text-muted mb-2"><i class="icofont-clock-time id-color me-1"></i>{{ $agenda->formattedTimeRange() }}</div>
                            @endif
                            <h4 class="mb-2"><a href="{{ route('public.agenda.show', $agenda->slug) }}" class="text-dark">{{ $agenda->title }}</a></h4>
                            <p class="mb-2"><i class="icofont-location-pin id-color me-1"></i>{{ $agenda->locationDisplay() }}</p>
                            <span class="badge bg-soft-primary">{{ $agenda->eventTypeLabel() }}</span>
                            <p class="mt-3 mb-0">{{ $agenda->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($agenda->content), 120) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="bg-light p-5 rounded-1 text-center">
                            <p class="mb-0">Tidak ada agenda {{ $filter === 'past' ? 'yang telah selesai' : 'mendatang' }}.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($agendas->hasPages())
                <div class="spacer-single"></div>
                {{ $agendas->links() }}
            @endif
        </div>
    </section>
@endsection
