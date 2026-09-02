@extends('layouts.app')

@section('title', 'Créer un compte')

@section('content')
    <p class="breadcrumb"><a href="{{ route('landing') }}">Accueil</a> / Créer un compte</p>
    @if($workshop)
        <h1 class="page-title">Ton compte <em>3x30</em></h1>
        <p class="page-sub">Entre le code reçu en fin d'atelier, ton prénom et ton email. Ce que tu écriras ensuite dans l'outil ne sera lisible que par toi.</p>
    @else
        <h1 class="page-title">Créer ton <em>compte membre</em></h1>
        <p class="page-sub">Utilise l'email avec lequel tu t'es inscrit·e au séminaire. Si tu viens de t'inscrire, l'accès est immédiat. Si tu viens d'un atelier 3x30, entre ton code d'atelier.</p>
    @endif

    <form class="stack" method="POST" action="{{ route('register') }}">
        @csrf
        @if($workshop)
            <div>
                <label for="workshop_code">Code d'atelier</label>
                <input type="text" id="workshop_code" name="workshop_code" value="{{ old('workshop_code', $code) }}" required autocapitalize="characters" autocomplete="off" style="letter-spacing:0.15em;text-transform:uppercase">
            </div>
        @endif
        <div>
            <label for="name">{{ $workshop ? 'Prénom et nom' : 'Nom' }}</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required {{ $workshop ? '' : 'autofocus' }}>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required minlength="8">
        </div>
        <div>
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8">
        </div>
        @unless($workshop)
            <div>
                <label for="workshop_code">Code d'atelier 3x30 (facultatif)</label>
                <input type="text" id="workshop_code" name="workshop_code" value="{{ old('workshop_code') }}" autocapitalize="characters" autocomplete="off">
            </div>
        @endunless
        <button type="submit" class="btn">Créer mon compte</button>
    </form>

    <p class="page-sub" style="margin-top:2rem;">Déjà un compte ? <a href="{{ route('login') }}">Connecte-toi</a>.</p>
@endsection
