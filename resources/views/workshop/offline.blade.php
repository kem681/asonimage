@extends('layouts.workshop')

@section('title', 'Hors ligne')

@section('content')
    <p class="eyebrow">Hors ligne</p>
    <h1 class="h1">Pas de <em>réseau</em></h1>
    <p class="lead">3x30 a besoin d'une connexion pour enregistrer ce que tu écris. Le geste, lui, ne dépend pas du réseau : tiens-le, tu le pointeras plus tard.</p>
    <div class="btn-row"><a href="{{ route('workshop.index') }}" class="btn secondary">Réessayer</a></div>
@endsection
