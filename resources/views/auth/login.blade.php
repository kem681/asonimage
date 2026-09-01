@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
    <p class="breadcrumb"><a href="{{ route('landing') }}">Accueil</a> / Connexion</p>
    <h1 class="page-title">Se <em>connecter</em></h1>
    <p class="page-sub">Accède aux ressources du séminaire avec ton compte membre.</p>

    <form class="stack" method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Se connecter</button>
    </form>

    <p class="page-sub" style="margin-top:2rem;">Pas encore de compte ? <a href="{{ route('register') }}">Crée-en un</a> avec l'email que tu as utilisé pour t'inscrire au séminaire.</p>
@endsection
