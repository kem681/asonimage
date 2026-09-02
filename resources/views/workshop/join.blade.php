@extends('layouts.workshop')

@section('title', "Code d'atelier")

@section('content')
    <p class="eyebrow">3x30</p>
    <h1 class="h1">Le code de <em>l'atelier</em></h1>
    <p class="lead">L'outil 3x30 est réservé aux hommes qui ont suivi un atelier. Entre le code reçu en fin d'atelier.</p>

    <form method="POST" action="{{ route('workshop.code') }}" class="stack">
        @csrf
        <div>
            <label for="workshop_code">Code d'atelier</label>
            <input type="text" id="workshop_code" name="workshop_code" required maxlength="32" value="{{ old('workshop_code') }}" autocapitalize="characters" autocomplete="off" style="letter-spacing:0.15em;text-transform:uppercase">
        </div>
        <button type="submit" class="btn">Entrer</button>
    </form>

    <p class="muted small" style="margin-top:1.5rem">Pas de code ? Demande-le à la personne qui a animé l'atelier, ou écris à contact@asonimage.ch.</p>
@endsection
