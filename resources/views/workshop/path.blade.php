@extends('layouts.workshop')

@section('title', 'Mon chemin')

@section('content')
    <p class="eyebrow">Mon chemin · {{ $content->axisLabel($anchor->axis) }}</p>
    <h1 class="h1">{{ $anchor->gesture }}</h1>
    <p class="lead">Depuis le {{ $cycleStart->translatedFormat('j F') }}. Je le dis à {{ $anchor->confidant }}.</p>

    <div class="card">
        <p class="eyebrow">Jour par jour</p>
        <div class="days">
            @foreach($days as $entry)
                @php $c = $entry['checkin']; @endphp
                <div class="day {{ $c ? ($c->held ? 'held' : 'missed') : '' }} {{ $entry['date']->isSameDay(\App\Services\Workshop\WorkshopClock::today()) ? 'today' : '' }}" title="{{ $entry['date']->translatedFormat('l j F') }}">{{ $entry['date']->format('j') }}</div>
            @endforeach
        </div>
        <p class="muted small">Doré : tenu. Sable : pas tenu. Vide : rien noté. Pas de compte, pas de série : juste ce qui a été.</p>
    </div>

    <a href="{{ $reviewDue ? route('workshop.review.create') : '#' }}" class="card card-link {{ $reviewDue ? 'accent' : '' }}">
        <p class="eyebrow">Fidélité</p>
        @if($reviewDue)
            <p class="card-title">La revue est ouverte</p>
            <p class="muted small">{{ $content->text('review_intro') }}</p>
        @else
            <p class="card-title">Revue le {{ $reviewDueOn->translatedFormat('l j F') }}</p>
            <p class="muted small">Dans {{ $daysUntilReview }} jour{{ $daysUntilReview > 1 ? 's' : '' }}. Prévois de la faire avec ton binôme.</p>
        @endif
    </a>

    <div class="card">
        <p class="eyebrow">Frottements nommés</p>
        @if($frictions->isEmpty())
            <p class="muted small">Aucun pour l'instant. <a href="{{ route('workshop.friction.create') }}">Celui de cette semaine</a>.</p>
        @else
            <ul class="list">
                @foreach($frictions as $friction)
                    <li>
                        <div class="date">Semaine du {{ $friction->week_start->translatedFormat('j F') }} · {{ $friction->isTold() ? 'dit à '.$friction->told_to : 'pas encore dit' }}</div>
                        <div>{{ $friction->body }}</div>
                    </li>
                @endforeach
            </ul>
            <p class="small" style="margin-top:0.6rem"><a href="{{ route('workshop.friction.create') }}">Celui de cette semaine</a></p>
        @endif
    </div>

    @if($reviews->isNotEmpty())
        <div class="card">
            <p class="eyebrow">Revues de ce geste</p>
            <ul class="list">
                @foreach($reviews as $review)
                    <li>
                        <div class="date">{{ $review->reviewed_on->translatedFormat('j F Y') }} · tenu : {{ $review->heldLabel() }}</div>
                        <div>{{ $review->changed }}</div>
                    </li>
                @endforeach
            </ul>
            <p class="small" style="margin-top:0.6rem"><a href="{{ route('workshop.memorial') }}">Tout le mémorial</a></p>
        </div>
    @endif

    <p class="muted small" style="margin-top:1rem">Changer de geste ? <a href="{{ route('workshop.anchor.create', ['axe' => $anchor->axis]) }}">En poser un nouveau</a> (celui-ci sera clos).</p>
@endsection
