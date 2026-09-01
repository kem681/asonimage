@extends('layouts.app')

@section('title', 'Créer un compte')

@section('content')
    <p class="breadcrumb"><a href="{{ route('landing') }}">Accueil</a> / Créer un compte</p>
    <h1 class="page-title">Créer ton <em>compte membre</em></h1>
    <p class="page-sub">Utilise l'email avec lequel tu t'es inscrit·e au séminaire. Si tu viens de t'inscrire, l'accès est immédiat.</p>

    <form class="stack" method="POST" action="{{ route('register') }}">
        @csrf
        <div>
            <label for="name">Nom</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
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
        <button type="submit" class="btn">Créer mon compte</button>
    </form>

    <p class="page-sub" style="margin-top:2rem;">Déjà un compte ? <a href="{{ route('login') }}">Connecte-toi</a>.</p>
@endsection
