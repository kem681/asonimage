@extends('layouts.app')

@section('title', 'Ressources')

@section('content')
    <h1 class="page-title">Les <em>ressources</em> du séminaire</h1>
    <p class="page-sub">Choisis une édition pour retrouver les contenus jour par jour.</p>

    @if($editions->isEmpty())
        <div class="empty-state">Aucune ressource n'a encore été publiée.</div>
    @else
        <div class="card-grid">
            @foreach($editions as $edition)
                <a href="{{ route('membres.edition', $edition) }}" class="card">
                    <div class="card-eyebrow">Édition</div>
                    <div class="card-title">{{ $edition->label }}</div>
                    <div class="card-meta">{{ $edition->resources_count }} ressource{{ $edition->resources_count > 1 ? 's' : '' }}</div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
