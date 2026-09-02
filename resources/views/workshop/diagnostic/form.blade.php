@extends('layouts.workshop')

@section('title', 'Diagnostic')

@section('content')
    <p class="eyebrow">Diagnostic</p>
    <h1 class="h1">Où ça <em>tire</em> aujourd'hui</h1>
    <p class="lead">{{ $content->text('diagnostic_intro') }}</p>

    <form method="POST" action="{{ route('workshop.diagnostic') }}" id="diagnostic-form">
        @csrf
        @foreach($content->statements() as $index => $statement)
            <div class="statement">
                <p class="text"><span class="muted small">{{ $index + 1 }}.</span> {{ $statement['text'] }}</p>
                <div class="scale">
                    @foreach($content->scale() as $value => $label)
                        <label>
                            <input type="radio" name="answers[{{ $index }}]" value="{{ $value }}" {{ (string) old("answers.$index") === (string) $value ? 'checked' : '' }} required>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="btn-row" style="margin-top:1.4rem">
            <button type="submit" class="btn block">Voir mon résultat</button>
        </div>
        <p class="muted small" style="margin-top:0.8rem">Aucune réponse n'est partagée. Ce que tu écris ici n'est lisible que par toi.</p>
    </form>
@endsection
