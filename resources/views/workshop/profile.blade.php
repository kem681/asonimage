@extends('layouts.workshop')

@section('title', 'Profil')

@section('content')
    <p class="eyebrow">Profil</p>
    <h1 class="h1">{{ $user->name }}</h1>
    <p class="lead">{{ $user->email }}@if($participant) · dans 3x30 depuis le {{ $participant->joined_at->timezone('Europe/Zurich')->translatedFormat('j F Y') }}@endif</p>

    <a href="{{ route('workshop.model') }}" class="card card-link">
        <p class="card-title">Le modèle</p>
        <p class="muted small">Relire les trois axes : filiation, désert, réponse à l'appel.</p>
    </a>
    <a href="{{ route('workshop.memorial') }}" class="card card-link">
        <p class="card-title">Mémorial</p>
        <p class="muted small">Tes revues, dans l'ordre.</p>
    </a>
    <a href="{{ route('workshop.install') }}" class="card card-link">
        <p class="card-title">Installer sur mon téléphone</p>
        <p class="muted small">Pour ouvrir 3x30 comme une application, depuis l'écran d'accueil.</p>
    </a>

    <div class="card quiet" style="margin-top:2rem">
        <p class="eyebrow">Confidentialité</p>
        <p class="small">Ce que tu écris ici (diagnostic, geste, frottements, revues) n'est lisible que par toi. Personne dans l'équipe ne peut le consulter. Tes compagnons de groupe voient ton prénom et la date du dernier échange, rien d'autre.</p>
    </div>

    <div class="card">
        <p class="eyebrow">Supprimer mon compte</p>
        <p class="small muted">Efface le compte et toutes tes données ({{ $anchorsCount }} geste{{ $anchorsCount > 1 ? 's' : '' }}, {{ $groupsCount }} groupe{{ $groupsCount > 1 ? 's' : '' }}). Définitif, rien n'est conservé.</p>
        <form method="POST" action="{{ route('workshop.profile.destroy') }}" class="stack" style="margin-top:0.8rem" onsubmit="return confirm('Supprimer définitivement ton compte et toutes tes données ?');">
            @csrf
            @method('DELETE')
            <div>
                <label for="confirmation">Écris SUPPRIMER pour confirmer</label>
                <input type="text" id="confirmation" name="confirmation" autocomplete="off" required>
            </div>
            <button type="submit" class="btn danger sm">Supprimer tout</button>
        </form>
    </div>
@endsection
