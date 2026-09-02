@extends('layouts.workshop')

@section('title', 'Fidélité')

@section('content')
    <p class="eyebrow">Fidélité · depuis le {{ $cycleStart->translatedFormat('j F') }}</p>
    <h1 class="h1">Ce qui a <em>tenu</em></h1>
    <p class="lead">{{ $content->text('review_intro') }}</p>

    <div class="card quiet">
        <p class="eyebrow">Le geste</p>
        <p class="card-title">{{ $anchor->gesture }}</p>
        <div class="days">
            @foreach($days as $entry)
                @php $c = $entry['checkin']; @endphp
                <div class="day {{ $c ? ($c->held ? 'held' : 'missed') : '' }}">{{ $entry['date']->format('j') }}</div>
            @endforeach
        </div>
        @if($frictions->isNotEmpty())
            <p class="eyebrow" style="margin-top:0.8rem">Les frottements nommés</p>
            <ul class="list">
                @foreach($frictions as $friction)
                    <li class="small">{{ $friction->body }} <span class="muted">({{ $friction->isTold() ? 'dit à '.$friction->told_to : 'pas dit' }})</span></li>
                @endforeach
            </ul>
        @endif
    </div>

    <form method="POST" action="{{ route('workshop.review.store') }}" class="stack">
        @csrf
        <div>
            <label>Le geste a tenu ?</label>
            <div class="choices">
                @foreach(\App\Models\Review::HELD_LABELS as $value => $label)
                    <label class="choice">
                        <input type="radio" name="held" value="{{ $value }}" {{ old('held') === $value ? 'checked' : '' }} required>
                        <span class="choice-title">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <p class="hint">« En partie » est une réponse valable, et attendue.</p>
        </div>
        <div>
            <label for="changed">Ce que ça a changé, ou pas</label>
            <textarea id="changed" name="changed" required maxlength="1000" placeholder="Une phrase. Ce qui a été donné, par qui.">{{ old('changed') }}</textarea>
        </div>
        <div>
            <label for="next_friction">Le prochain point de friction (pas encore résolu)</label>
            <textarea id="next_friction" name="next_friction" maxlength="1000">{{ old('next_friction') }}</textarea>
        </div>
        <div>
            <label>Et maintenant</label>
            <div class="choices">
                <label class="choice">
                    <input type="radio" name="decision" value="{{ \App\Models\Review::DECISION_CONTINUE }}" {{ old('decision', 'continuer') === 'continuer' ? 'checked' : '' }}>
                    <span><span class="choice-title">Je continue le même geste</span><br><span class="choice-sub">Il n'a pas fini son travail.</span></span>
                </label>
                <label class="choice">
                    <input type="radio" name="decision" value="{{ \App\Models\Review::DECISION_NEW_GESTURE }}" {{ old('decision') === 'nouveau' ? 'checked' : '' }}>
                    <span><span class="choice-title">Un nouveau geste, même axe</span><br><span class="choice-sub">Toujours un seul, toujours minuscule.</span></span>
                </label>
                <label class="choice">
                    <input type="radio" name="decision" value="{{ \App\Models\Review::DECISION_DIAGNOSTIC }}" {{ old('decision') === 'diagnostic' ? 'checked' : '' }}>
                    <span><span class="choice-title">Je refais le diagnostic</span><br><span class="choice-sub">Pour voir où ça tire maintenant.</span></span>
                </label>
            </div>
        </div>
        <button type="submit" class="btn">Garder en mémorial</button>
    </form>
@endsection
