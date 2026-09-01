@extends('layouts.app')

@section('title', $edition->label)

@section('content')
    <p class="breadcrumb"><a href="{{ route('membres.index') }}">Ressources</a> / {{ $edition->label }}</p>
    <h1 class="page-title">{{ $edition->label }} — <em>par jour</em></h1>
    <p class="page-sub">Sélectionne un jour du séminaire pour voir les contenus disponibles.</p>

    <div class="card-grid">
        @for($day = 1; $day <= 5; $day++)
            <a href="{{ route('membres.jour', [$edition, $day]) }}" class="card">
                <div class="card-eyebrow">Jour {{ $day }}</div>
                <div class="card-title">J{{ $day }}</div>
                <div class="card-count">{{ $counts[$day] ?? 0 }} ressource{{ ($counts[$day] ?? 0) > 1 ? 's' : '' }}</div>
            </a>
        @endfor
    </div>
@endsection
