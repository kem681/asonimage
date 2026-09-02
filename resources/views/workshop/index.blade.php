@extends('layouts.workshop')

@section('title', "Aujourd'hui")

@section('content')
    <p class="eyebrow">{{ $today->translatedFormat('l j F') }}</p>
    <h1 class="h1">Bonjour <em>{{ auth()->user()->firstName() }}</em></h1>

    @if(! $diagnostic)
        <p class="lead">Trois étapes de la vie de Jean-Baptiste, trois axes : filiation, désert, réponse à l'appel. Le diagnostic te dit où ça tire le plus aujourd'hui. Ensuite, un seul geste.</p>
        <div class="card accent">
            <p class="eyebrow">Première étape</p>
            <h2 class="card-title">Le diagnostic</h2>
            <p class="muted">{{ $content->text('diagnostic_intro') }}</p>
            <div class="btn-row"><a href="{{ route('workshop.diagnostic') }}" class="btn">Commencer</a></div>
        </div>
        <div class="card quiet">
            <p class="muted">Tu préfères d'abord relire le modèle ? <a href="{{ route('workshop.model') }}">Les trois axes</a>.</p>
        </div>
    @elseif(! $diagnostic->hasAxis())
        <div class="card accent">
            <p class="eyebrow">Diagnostic fait</p>
            <h2 class="card-title">Choisis ton axe phare</h2>
            <p class="muted">Plusieurs axes sont à égalité. À toi de dire par lequel commencer.</p>
            <div class="btn-row"><a href="{{ route('workshop.diagnostic.result') }}" class="btn">Voir le résultat</a></div>
        </div>
    @elseif(! $anchor)
        <div class="card accent">
            <p class="eyebrow">Axe phare : {{ $content->axisLabel($diagnostic->axis) }}</p>
            <h2 class="card-title">Pose ton geste</h2>
            <p class="muted">{{ $content->text('anchor_intro') }}</p>
            <div class="btn-row">
                <a href="{{ route('workshop.anchor.create') }}" class="btn">Poser mon geste</a>
                <a href="{{ route('workshop.diagnostic.result') }}" class="btn secondary">Revoir le diagnostic</a>
            </div>
        </div>
    @else
        <div class="card accent">
            <p class="eyebrow">Ce que je porte · {{ $content->axisLabel($anchor->axis) }}</p>
            <p class="card-title">{{ $anchor->gesture }}</p>
            <p class="muted small">Mon manquement : {{ $anchor->manquement }}</p>
            <p class="muted small">Je le dis à {{ $anchor->confidant }}.</p>
        </div>

        <div class="card">
            <p class="eyebrow">Aujourd'hui</p>
            @if($todayCheckin)
                <p class="card-title">{{ $todayCheckin->held ? 'Tenu.' : 'Pas tenu.' }}</p>
                <p class="muted small">{{ $todayCheckin->held ? 'Bien. Rien à ajouter.' : $content->text('checkin_missed') }}</p>
                <details style="margin-top:0.6rem">
                    <summary class="small muted" style="cursor:pointer">Modifier</summary>
                    <div class="btn-row">
                        <form method="POST" action="{{ route('workshop.anchor.checkin') }}">@csrf<input type="hidden" name="day" value="{{ $today->toDateString() }}"><input type="hidden" name="held" value="1"><button class="btn sm gold" type="submit">Tenu</button></form>
                        <form method="POST" action="{{ route('workshop.anchor.checkin') }}">@csrf<input type="hidden" name="day" value="{{ $today->toDateString() }}"><input type="hidden" name="held" value="0"><button class="btn sm secondary" type="submit">Pas tenu</button></form>
                    </div>
                </details>
            @else
                <p class="card-title">Le geste a tenu ?</p>
                <div class="btn-row">
                    <form method="POST" action="{{ route('workshop.anchor.checkin') }}">@csrf<input type="hidden" name="day" value="{{ $today->toDateString() }}"><input type="hidden" name="held" value="1"><button class="btn gold" type="submit">Tenu</button></form>
                    <form method="POST" action="{{ route('workshop.anchor.checkin') }}">@csrf<input type="hidden" name="day" value="{{ $today->toDateString() }}"><input type="hidden" name="held" value="0"><button class="btn secondary" type="submit">Pas tenu</button></form>
                </div>
            @endif

            @if(! $yesterdayCheckin && $anchor->started_on->toDateString() < $today->toDateString())
                <p class="muted small" style="margin-top:1rem">Et hier ({{ $yesterday->translatedFormat('l') }}) ?</p>
                <div class="btn-row">
                    <form method="POST" action="{{ route('workshop.anchor.checkin') }}">@csrf<input type="hidden" name="day" value="{{ $yesterday->toDateString() }}"><input type="hidden" name="held" value="1"><button class="btn sm secondary" type="submit">Tenu</button></form>
                    <form method="POST" action="{{ route('workshop.anchor.checkin') }}">@csrf<input type="hidden" name="day" value="{{ $yesterday->toDateString() }}"><input type="hidden" name="held" value="0"><button class="btn sm secondary" type="submit">Pas tenu</button></form>
                </div>
            @endif
        </div>

        <a href="{{ route('workshop.friction.create') }}" class="card card-link">
            <p class="eyebrow">Cette semaine</p>
            <p class="card-title">Frottement nommé</p>
            @if(! $weekFriction)
                <p class="muted small">Pas encore écrit. Où la résistance s'est-elle manifestée ?</p>
            @elseif(! $weekFriction->isTold())
                <p class="muted small">Écrit. Il attend d'être dit à quelqu'un.</p>
            @else
                <p class="muted small">Écrit, et dit à {{ $weekFriction->told_to }}.</p>
            @endif
        </a>

        <a href="{{ $anchor->isReviewDue() ? route('workshop.review.create') : route('workshop.path') }}" class="card card-link">
            <p class="eyebrow">Fidélité</p>
            @if($anchor->isReviewDue())
                <p class="card-title">La revue est ouverte</p>
                <p class="muted small">Quatre semaines ont passé. Regarde en arrière.</p>
            @else
                <p class="card-title">Revue le {{ $anchor->reviewDueOn()->translatedFormat('j F') }}</p>
                <p class="muted small">Dans {{ $anchor->daysUntilReview() }} jour{{ $anchor->daysUntilReview() > 1 ? 's' : '' }}. D'ici là, un jour à la fois.</p>
            @endif
        </a>
    @endif

    @if(count($reminders) > 1 || ($anchor && count($reminders)))
        <p class="eyebrow" style="margin-top:1.6rem">Rappels</p>
        @foreach($reminders as $reminder)
            @continue(in_array($reminder['key'], ['checkin', 'diagnostic', 'choose_axis', 'anchor']))
            <a href="{{ route($reminder['route'], $reminder['params']) }}" class="reminder">
                <div>
                    <div class="r-title">{{ $reminder['title'] }}</div>
                    <div class="r-body">{{ $reminder['body'] }}</div>
                </div>
            </a>
        @endforeach
    @endif

    @if($groups->isNotEmpty())
        <p class="eyebrow" style="margin-top:1.6rem">Mes groupes</p>
        @foreach($groups as $group)
            <a href="{{ route('workshop.group.show', $group) }}" class="card card-link">
                <p class="card-title">{{ $group->name }} @if($group->isSilent())<span class="tag soft">silencieux</span>@endif</p>
                <p class="muted small">Dernier échange déclaré il y a {{ $group->daysSinceLastContact() }} jour{{ $group->daysSinceLastContact() > 1 ? 's' : '' }}.</p>
            </a>
        @endforeach
    @endif

    <p class="muted small" style="margin-top:2rem">
        <a href="{{ route('workshop.model') }}">Relire les trois axes</a> ·
        <a href="{{ route('workshop.memorial') }}">Mémorial</a>
    </p>
@endsection
