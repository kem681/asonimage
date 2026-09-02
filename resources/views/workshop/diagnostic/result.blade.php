@extends('layouts.workshop')

@section('title', 'Résultat')

@section('content')
    <p class="eyebrow">Diagnostic du {{ $diagnostic->completed_at->timezone('Europe/Zurich')->translatedFormat('j F Y') }}</p>
    <h1 class="h1">Où tu te <em>situes</em></h1>
    <p class="lead">{{ $content->text('result_intro') }}</p>

    <div class="card">
        @foreach($diagnostic->scores() as $key => $score)
            @php $isLead = $diagnostic->axis === $key; @endphp
            <div class="axis-row {{ $isLead ? 'lead' : '' }}">
                <div class="axis-head">
                    <span class="axis-name">{{ $content->axisLabel($key) }} @if($isLead)<span class="tag">axe phare</span>@endif</span>
                    <span class="muted small">{{ $score }} / 100</span>
                </div>
                <div class="bar"><span style="width: {{ $score }}%"></span></div>
            </div>
        @endforeach
    </div>

    @if(! $diagnostic->hasAxis())
        <div class="card accent">
            <p class="eyebrow">Égalité</p>
            <p class="card-title">Choisis ton axe phare</p>
            <p class="muted small">Ces axes tirent autant l'un que l'autre. Par lequel veux-tu commencer ?</p>
            <form method="POST" action="{{ route('workshop.diagnostic.axis') }}" class="stack" style="margin-top:0.8rem">
                @csrf
                <div class="choices">
                    @foreach($leading as $key)
                        <label class="choice">
                            <input type="radio" name="axis" value="{{ $key }}" required>
                            <span><span class="choice-title">{{ $content->axisLabel($key) }}</span><br><span class="choice-sub">{{ $content->axis($key)['title'] }}</span></span>
                        </label>
                    @endforeach
                </div>
                <button type="submit" class="btn">C'est celui-là</button>
            </form>
        </div>
    @else
        <div class="card accent">
            <p class="eyebrow">Axe phare</p>
            <p class="card-title">{{ $content->axis($diagnostic->axis)['title'] }}</p>
            <p class="muted small">{{ $content->axis($diagnostic->axis)['summary'] }}</p>
            <div class="btn-row">
                @if($anchor)
                    <a href="{{ route('workshop.path') }}" class="btn">Voir mon chemin</a>
                @else
                    <a href="{{ route('workshop.anchor.create') }}" class="btn">Poser mon geste</a>
                @endif
                <a href="{{ route('workshop.axis', $diagnostic->axis) }}" class="btn secondary">Lire cet axe</a>
            </div>
        </div>
        <p class="muted small">Les deux autres axes restent à lire : <a href="{{ route('workshop.model') }}">le modèle</a>. Ils ne se travaillent pas maintenant.</p>
    @endif
@endsection
