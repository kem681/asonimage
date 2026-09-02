@extends('layouts.workshop')

@section('title', 'Mémorial')

@section('content')
    <p class="eyebrow">Mémorial</p>
    <h1 class="h1">Ce qui a été <em>donné</em></h1>
    <p class="lead">{{ $content->text('memorial_intro') }}</p>

    @if($reviews->isEmpty())
        <div class="card quiet">
            <p class="muted">Aucune pierre encore. La première se pose à la revue, quatre semaines après ton premier geste.</p>
        </div>
    @else
        @foreach($reviews as $review)
            <div class="card">
                <div class="date">{{ $review->reviewed_on->translatedFormat('j F Y') }} · {{ $content->axisLabel($review->anchor->axis) }}</div>
                <p class="card-title">{{ $review->anchor->gesture }}</p>
                <p><strong>Tenu :</strong> {{ $review->heldLabel() }}</p>
                <p>{{ $review->changed }}</p>
                @if($review->next_friction)
                    <p class="muted small">Prochain point de friction : {{ $review->next_friction }}</p>
                @endif
            </div>
        @endforeach
    @endif
@endsection
