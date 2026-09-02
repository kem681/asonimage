@extends('layouts.workshop')

@section('title', $axis['label'])

@section('content')
    <p class="eyebrow"><a href="{{ route('workshop.model') }}">Le modèle</a> / {{ $axis['label'] }} @if($diagnostic && $diagnostic->axis === $key)<span class="tag">axe phare</span>@endif</p>
    <h1 class="h1">{{ $axis['title'] }}</h1>
    <p class="lead">{{ $axis['summary'] }}</p>

    @foreach($axis['body'] as $paragraph)
        <p style="margin-bottom:0.9rem">{{ $paragraph }}</p>
    @endforeach

    <div class="card quiet" style="margin-top:1.4rem">
        <p class="eyebrow">La question</p>
        <p class="card-title" style="font-style:italic">{{ $axis['question'] }}</p>
    </div>

    <div class="card">
        <p class="eyebrow">Exemples de gestes</p>
        <ul class="list">
            @foreach($axis['gestures'] as $gesture)
                <li>{{ $gesture }}</li>
            @endforeach
        </ul>
    </div>

    @if($diagnostic && $diagnostic->hasAxis())
        <div class="btn-row">
            <a href="{{ route('workshop.anchor.create', ['axe' => $key]) }}" class="btn secondary">Poser un geste sur cet axe</a>
        </div>
        @if($diagnostic->axis !== $key)
            <p class="muted small" style="margin-top:0.6rem">Ton axe phare est « {{ $content->axisLabel($diagnostic->axis) }} ». Un seul geste à la fois : si tu en poses un ici, il remplacera celui en cours.</p>
        @endif
    @endif
@endsection
