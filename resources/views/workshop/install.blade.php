@extends('layouts.workshop')

@section('title', 'Installer')

@section('content')
    <p class="eyebrow">Installer</p>
    <h1 class="h1">3x30 sur ton <em>écran d'accueil</em></h1>
    <p class="lead">Pas de store, pas de téléchargement : le site s'installe comme une application, en trente secondes.</p>

    <div class="card" id="android-card">
        <p class="eyebrow">Android (Chrome)</p>
        <ol style="margin-left:1.2rem">
            <li>Touche le bouton ci-dessous, ou le menu ⋮ en haut à droite de Chrome.</li>
            <li>Choisis « Installer l'application » (ou « Ajouter à l'écran d'accueil »).</li>
            <li>Confirme. L'icône 3x30 apparaît sur ton écran d'accueil.</li>
        </ol>
        <div class="btn-row"><button type="button" class="btn" id="install-now">Installer maintenant</button></div>
        <p class="muted small" id="install-hint" style="margin-top:0.6rem">Si le bouton ne réagit pas, passe par le menu ⋮ de Chrome.</p>
    </div>

    <div class="card">
        <p class="eyebrow">iPhone (Safari)</p>
        <ol style="margin-left:1.2rem">
            <li>Ouvre cette page dans Safari (pas dans Instagram ou WhatsApp).</li>
            <li>Touche le bouton Partager (le carré avec une flèche vers le haut).</li>
            <li>Fais défiler et choisis « Sur l'écran d'accueil », puis « Ajouter ».</li>
        </ol>
    </div>

    <div class="card quiet">
        <p class="muted small">Une fois installée, l'application s'ouvre directement sur « Aujourd'hui ». Elle se met à jour toute seule.</p>
        @auth
            <p class="small" style="margin-top:0.5rem"><a href="{{ route('workshop.index') }}">Retour à aujourd'hui</a></p>
        @else
            <p class="small" style="margin-top:0.5rem"><a href="{{ route('login') }}">Connexion</a> · <a href="{{ route('workshop.register') }}">Créer mon compte avec mon code d'atelier</a></p>
        @endauth
    </div>
@endsection

@push('scripts')
<script>
(function () {
    var deferred = null;
    var button = document.getElementById('install-now');
    window.addEventListener('beforeinstallprompt', function (e) { e.preventDefault(); deferred = e; });
    if (button) {
        button.addEventListener('click', function () {
            if (deferred) { deferred.prompt(); deferred.userChoice.then(function () { deferred = null; }); }
            else { document.getElementById('install-hint').textContent = "Passe par le menu ⋮ de Chrome, puis « Installer l'application ». Sur iPhone, utilise Safari et le bouton Partager."; }
        });
    }
})();
</script>
@endpush
