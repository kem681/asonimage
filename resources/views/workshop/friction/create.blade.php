@extends('layouts.workshop')

@section('title', 'Frottement nommé')

@section('content')
    <p class="eyebrow">Frottement nommé · semaine du {{ $weekStart->translatedFormat('j F') }}</p>
    <h1 class="h1">Où ça a <em>résisté</em></h1>
    <p class="lead">{{ $content->text('friction_intro') }}</p>

    <div class="card quiet">
        <p class="muted small">Le geste : {{ $anchor->gesture }}</p>
    </div>

    <form method="POST" action="{{ route('workshop.friction.store') }}" class="stack">
        @csrf
        <div>
            <label for="body">Où la résistance s'est manifestée</label>
            <textarea id="body" name="body" required maxlength="1000" placeholder="L'orgueil précis, la fuite précise. Une ligne suffit.">{{ old('body', $friction?->body) }}</textarea>
        </div>
        <div>
            <label for="told_to">À qui je le dis</label>
            <input type="text" id="told_to" name="told_to" maxlength="120" value="{{ old('told_to', $friction?->told_to ?? $anchor->confidant) }}" placeholder="Un prénom réel">
        </div>
        <label class="choice">
            <input type="checkbox" name="told" value="1" {{ old('told', $friction?->isTold()) ? 'checked' : '' }}>
            <span><span class="choice-title">Je le lui ai déjà dit</span><br><span class="choice-sub">Sinon, laisse décoché : l'outil te le rappellera.</span></span>
        </label>
        <button type="submit" class="btn">{{ $friction ? 'Mettre à jour' : 'Nommer' }}</button>
    </form>

    @if($previous->isNotEmpty())
        <p class="eyebrow" style="margin-top:2rem">Semaines précédentes</p>
        @foreach($previous as $past)
            <div class="card">
                <div class="date">Semaine du {{ $past->week_start->translatedFormat('j F') }}</div>
                <p>{{ $past->body }}</p>
                @if($past->isTold())
                    <p class="muted small">Dit à {{ $past->told_to }} le {{ $past->told_on->translatedFormat('j F') }}.</p>
                @else
                    <form method="POST" action="{{ route('workshop.friction.told', $past) }}" class="stack" style="margin-top:0.6rem">
                        @csrf
                        <div>
                            <label for="told_to_{{ $past->id }}">Pas encore dit. À qui ?</label>
                            <input type="text" id="told_to_{{ $past->id }}" name="told_to" required maxlength="120" value="{{ $past->told_to ?? $anchor->confidant }}">
                        </div>
                        <button type="submit" class="btn sm secondary">Je l'ai dit</button>
                    </form>
                @endif
            </div>
        @endforeach
    @endif
@endsection
