@extends('layouts.workshop')

@section('title', 'Poser mon geste')

@section('content')
    <p class="eyebrow">Ancrage · {{ $content->axisLabel($axis) }}</p>
    <h1 class="h1">Un seul <em>geste</em></h1>
    <p class="lead">{{ $content->text('anchor_intro') }}</p>

    @if($current)
        <div class="card quiet">
            <p class="muted small">Tu portes déjà un geste (« {{ $current->gesture }} »). En poser un nouveau clôt celui-là. Un seul à la fois.</p>
        </div>
    @endif

    <div class="card quiet">
        <p class="eyebrow">Exemples sur cet axe</p>
        <ul class="list">
            @foreach($content->axis($axis)['gestures'] as $example)
                <li class="small">{{ $example }}</li>
            @endforeach
        </ul>
    </div>

    <form method="POST" action="{{ route('workshop.anchor.store') }}" class="stack">
        @csrf
        <input type="hidden" name="axis" value="{{ $axis }}">
        <div>
            <label for="manquement">Mon manquement, en une phrase</label>
            <textarea id="manquement" name="manquement" required maxlength="500" placeholder="Ce qui tire, dit simplement.">{{ old('manquement') }}</textarea>
        </div>
        <div>
            <label for="gesture">Le geste (un seul, minuscule)</label>
            <textarea id="gesture" name="gesture" required maxlength="500" placeholder="Tenable même un mauvais jour.">{{ old('gesture') }}</textarea>
            <p class="hint">S'il dépend de ta fatigue ou de ton humeur, il est trop grand. Réduis-le.</p>
        </div>
        <div>
            <label for="confidant">À qui je le dis</label>
            <input type="text" id="confidant" name="confidant" required maxlength="120" value="{{ old('confidant') }}" placeholder="Un prénom réel">
            <p class="hint">{{ $content->text('anchor_confidant') }}</p>
        </div>
        <button type="submit" class="btn">Poser ce geste</button>
    </form>

    <p class="muted small" style="margin-top:1.2rem">Autre axe ? <a href="{{ route('workshop.model') }}">Relire le modèle</a>.</p>
@endsection
