@extends('layouts.live')

@section('title', 'Sondages')

@section('nav')
    <a href="{{ route('membres.index') }}">Ressources</a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Déconnexion</button></form>
@endsection

@section('content')
    <p class="eyebrow">Animateur</p>
    <h1 class="h1">Sondages <em>en direct</em></h1>
    <p class="lead">La salle répond sur <strong>{{ rtrim(config('app.url'), '/') }}/live</strong>, l'écran à projeter est sur <strong>/live/ecran</strong>. Un seul sondage est en ligne à la fois.</p>

    <div class="card accent">
        <p class="eyebrow">Créer un sondage</p>
        <form class="stack" method="POST" action="{{ route('admin.live.store') }}">
            @csrf
            <div>
                <label for="template">À partir d'un modèle</label>
                <select id="template" name="template">
                    <option value="">Vide (j'ajoute mes questions ensuite)</option>
                    @foreach ($templates as $key => $title)
                        <option value="{{ $key }}">{{ $title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="title">Titre (si vide)</label>
                <input type="text" id="title" name="title" maxlength="160" placeholder="Atelier du 5 octobre">
            </div>
            <button type="submit" class="btn">Créer</button>
        </form>
    </div>

    <ul class="list">
        @forelse ($sessions as $s)
            <li>
                <div style="display:flex; justify-content:space-between; gap:1rem; align-items:baseline;">
                    <a href="{{ route('admin.live.show', $s) }}" style="font-family:'Cormorant Garamond',serif; font-size:1.3rem; text-decoration:none;">{{ $s->title }}</a>
                    <span>@if ($s->is_active)<span class="tag open">En ligne</span>@endif <a class="btn sm" href="{{ route('admin.live.presenter', $s) }}">Présenter</a></span>
                </div>
                <p class="muted small">Code {{ $s->code }} · {{ $s->questions_count }} question{{ $s->questions_count > 1 ? 's' : '' }} · créé le {{ $s->created_at->format('d.m.Y') }}</p>
            </li>
        @empty
            <li class="muted">Aucun sondage pour l'instant.</li>
        @endforelse
    </ul>
@endsection
