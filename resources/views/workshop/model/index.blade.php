@extends('layouts.workshop')

@section('title', 'Le modèle')

@section('content')
    <p class="eyebrow">Le modèle</p>
    <h1 class="h1">Trois étapes, <em>trois axes</em></h1>
    <p class="lead">Jean-Baptiste s'inscrit dans une histoire, traverse le désert, répond à un appel. Les trois se rejouent dans ta vie, à toutes les échelles. Ils se lisent librement ; ils se travaillent un à la fois.</p>

    @foreach($content->axes() as $key => $axis)
        <a href="{{ route('workshop.axis', $key) }}" class="card card-link {{ $diagnostic && $diagnostic->axis === $key ? 'accent' : '' }}">
            <p class="eyebrow">{{ $axis['label'] }} @if($diagnostic && $diagnostic->axis === $key)<span class="tag">axe phare</span>@endif</p>
            <p class="card-title">{{ $axis['title'] }}</p>
            <p class="muted small">{{ $axis['summary'] }}</p>
        </a>
    @endforeach
@endsection
