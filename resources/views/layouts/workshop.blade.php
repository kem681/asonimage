<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>@yield('title', '3x30') — À Son Image</title>
<link rel="manifest" href="/pwa/manifest.webmanifest">
<meta name="theme-color" content="#3A4A3A">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="3x30">
<link rel="apple-touch-icon" href="/pwa/icon-180.png">
<link rel="icon" type="image/png" sizes="192x192" href="/pwa/icon-192.png">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
<style>
:root {
  --sand: #F5F0E8; --sand-dark: #E8E0D0; --earth: #8B7355; --earth-light: #A89070;
  --forest: #3A4A3A; --forest-light: #4A5E4A; --gold: #C4A35A; --gold-light: #D4B86A;
  --cream: #FFFDF7; --ink: #2A2A25; --ink-soft: #4A4A42; --warm-white: #FAF8F3;
  --danger: #b3413a;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
html { -webkit-text-size-adjust: 100%; }
body { font-family: 'DM Sans', sans-serif; background: var(--warm-white); color: var(--ink); min-height: 100vh; display: flex; flex-direction: column; line-height: 1.5; }
a { color: var(--forest); }

.topbar { background: var(--forest); color: var(--cream); padding: calc(0.8rem + env(safe-area-inset-top)) 1.2rem 0.8rem; display: flex; align-items: center; justify-content: space-between; }
.topbar a { color: inherit; text-decoration: none; }
.topbar .brand { font-family: 'Cormorant Garamond', serif; font-size: 1.35rem; font-weight: 500; letter-spacing: 0.02em; }
.topbar .brand em { color: var(--gold); font-style: italic; }
.topbar .brand small { font-family: 'DM Sans', sans-serif; font-size: 0.7rem; letter-spacing: 0.18em; text-transform: uppercase; opacity: 0.75; margin-left: 0.6rem; }
.topbar nav a { font-size: 0.78rem; letter-spacing: 0.06em; opacity: 0.85; margin-left: 1rem; }

main { flex: 1; width: 100%; max-width: 640px; margin: 0 auto; padding: 1.4rem 1.2rem calc(5.5rem + env(safe-area-inset-bottom)); }

.status { background: var(--sand); border-left: 3px solid var(--gold); padding: 0.9rem 1.1rem; margin-bottom: 1.4rem; font-size: 0.92rem; }
.errors { background: #fbeceb; border-left: 3px solid var(--danger); padding: 0.9rem 1.1rem; margin-bottom: 1.4rem; font-size: 0.92rem; color: #7a2b26; }
.errors ul { margin-left: 1.1rem; }

.eyebrow { font-size: 0.7rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--earth-light); margin-bottom: 0.4rem; }
.h1 { font-family: 'Cormorant Garamond', serif; font-size: clamp(1.8rem, 6vw, 2.4rem); font-weight: 400; line-height: 1.15; margin-bottom: 0.6rem; }
.h1 em { font-style: italic; color: var(--earth); }
.h2 { font-family: 'Cormorant Garamond', serif; font-size: 1.45rem; font-weight: 500; margin-bottom: 0.5rem; }
.lead { color: var(--ink-soft); font-weight: 300; margin-bottom: 1.6rem; }
.muted { color: var(--ink-soft); font-weight: 300; font-size: 0.9rem; }
.small { font-size: 0.82rem; }
p + p { margin-top: 0.8rem; }

.card { background: var(--cream); border: 1px solid var(--sand-dark); padding: 1.3rem 1.2rem; margin-bottom: 1.1rem; position: relative; }
.card.accent { border-top: 3px solid var(--gold); }
.card.quiet { background: var(--sand); border-color: transparent; }
.card .eyebrow { margin-bottom: 0.5rem; }
.card-title { font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; margin-bottom: 0.4rem; }
.card-link { display: block; text-decoration: none; color: inherit; }
.card-link:active { background: var(--sand); }

.btn { display: inline-block; padding: 0.9rem 1.6rem; background: var(--forest); color: var(--cream); border: none; font-family: inherit; font-size: 0.82rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; cursor: pointer; text-decoration: none; text-align: center; }
.btn:hover { background: var(--ink); }
.btn.block { display: block; width: 100%; }
.btn.secondary { background: transparent; color: var(--forest); border: 1px solid var(--forest); }
.btn.gold { background: var(--gold); color: var(--ink); }
.btn.danger { background: var(--danger); }
.btn.sm { padding: 0.6rem 1rem; font-size: 0.72rem; }
.btn-row { display: flex; gap: 0.6rem; flex-wrap: wrap; margin-top: 0.8rem; }
.btn-row form { flex: 1; }
.btn-row .btn { width: 100%; }
.link-btn { background: none; border: none; color: var(--forest); font: inherit; text-decoration: underline; cursor: pointer; padding: 0; }

form.stack { display: flex; flex-direction: column; gap: 1.1rem; }
form.stack label { font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--earth); font-weight: 500; margin-bottom: 0.35rem; display: block; }
form.stack input, form.stack select, form.stack textarea { width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--sand-dark); background: var(--cream); font-family: inherit; font-size: 1rem; color: var(--ink); outline: none; border-radius: 0; }
form.stack textarea { min-height: 6rem; resize: vertical; }
form.stack input:focus, form.stack textarea:focus, form.stack select:focus { border-color: var(--gold); }
form.stack .hint { font-size: 0.82rem; color: var(--ink-soft); font-weight: 300; margin-top: 0.35rem; }
form.stack .btn { align-self: stretch; margin-top: 0.4rem; }

.choices { display: flex; flex-direction: column; gap: 0.5rem; }
.choice { display: flex; align-items: flex-start; gap: 0.7rem; padding: 0.85rem 0.95rem; border: 1px solid var(--sand-dark); background: var(--cream); cursor: pointer; }
.choice input { margin-top: 0.25rem; accent-color: var(--forest); flex: none; }
.choice:has(input:checked) { border-color: var(--gold); background: var(--sand); }
.choice .choice-title { font-weight: 500; }
.choice .choice-sub { font-size: 0.82rem; color: var(--ink-soft); font-weight: 300; }

.statement { padding: 1rem 0; border-bottom: 1px solid var(--sand-dark); }
.statement .text { font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; line-height: 1.3; margin-bottom: 0.7rem; }
.scale { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.4rem; }
.scale label { display: block; text-align: center; padding: 0.65rem 0.2rem; border: 1px solid var(--sand-dark); background: var(--cream); font-size: 0.72rem; letter-spacing: 0.02em; cursor: pointer; }
.scale input { position: absolute; opacity: 0; width: 0; height: 0; }
.scale label:has(input:checked) { background: var(--forest); color: var(--cream); border-color: var(--forest); }

.axis-row { margin-bottom: 1rem; }
.axis-row .axis-head { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem; }
.axis-row .axis-name { font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; }
.axis-row.lead .axis-name { color: var(--earth); font-weight: 600; }
.bar { height: 10px; background: var(--sand-dark); overflow: hidden; }
.bar > span { display: block; height: 100%; background: var(--earth-light); }
.axis-row.lead .bar > span { background: var(--gold); }
.tag { display: inline-block; font-size: 0.66rem; letter-spacing: 0.16em; text-transform: uppercase; padding: 0.2rem 0.55rem; background: var(--gold); color: var(--ink); margin-left: 0.5rem; vertical-align: middle; }
.tag.soft { background: var(--sand-dark); color: var(--ink-soft); }

.days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.45rem; margin: 0.8rem 0; }
.day { aspect-ratio: 1; border-radius: 50%; border: 1px solid var(--sand-dark); background: var(--cream); display: flex; align-items: center; justify-content: center; font-size: 0.65rem; color: var(--ink-soft); }
.day.held { background: var(--gold); border-color: var(--gold); color: var(--ink); }
.day.missed { background: var(--sand); border-color: var(--sand-dark); }
.day.today { outline: 2px solid var(--forest); outline-offset: 1px; }

.reminder { display: flex; gap: 0.8rem; align-items: flex-start; padding: 0.9rem 1rem; background: var(--sand); border-left: 3px solid var(--gold); margin-bottom: 0.6rem; text-decoration: none; color: inherit; }
.reminder .r-title { font-weight: 500; }
.reminder .r-body { font-size: 0.85rem; color: var(--ink-soft); font-weight: 300; }

.list { list-style: none; }
.list li { padding: 0.8rem 0; border-bottom: 1px solid var(--sand-dark); }
.list li:last-child { border-bottom: none; }
.date { font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--earth-light); }
.code-big { font-family: 'Cormorant Garamond', serif; font-size: 2.4rem; letter-spacing: 0.25em; text-align: center; padding: 0.8rem; background: var(--sand); margin: 0.6rem 0; user-select: all; }
.chips { display: flex; flex-wrap: wrap; gap: 0.4rem; }
.chip { padding: 0.35rem 0.7rem; background: var(--sand); font-size: 0.85rem; }

.nav-bottom { position: fixed; left: 0; right: 0; bottom: 0; background: var(--cream); border-top: 1px solid var(--sand-dark); display: grid; grid-template-columns: repeat(4, 1fr); padding-bottom: env(safe-area-inset-bottom); z-index: 10; }
.nav-bottom a { text-align: center; padding: 0.7rem 0.2rem 0.6rem; font-size: 0.66rem; letter-spacing: 0.08em; text-transform: uppercase; color: var(--ink-soft); text-decoration: none; }
.nav-bottom a .ico { display: block; font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; line-height: 1.2; color: var(--earth-light); }
.nav-bottom a.active { color: var(--forest); font-weight: 600; }
.nav-bottom a.active .ico { color: var(--gold); }

.install-banner { display: none; background: var(--forest); color: var(--cream); padding: 0.8rem 1.2rem; font-size: 0.85rem; align-items: center; justify-content: space-between; gap: 1rem; }
.install-banner a, .install-banner button { color: var(--gold); background: none; border: 1px solid var(--gold); padding: 0.4rem 0.8rem; font: inherit; font-size: 0.75rem; letter-spacing: 0.08em; text-transform: uppercase; text-decoration: none; cursor: pointer; white-space: nowrap; }

@media (min-width: 720px) {
  main { padding-top: 2.4rem; }
  .nav-bottom { max-width: 640px; margin: 0 auto; }
}
</style>
@stack('head')
</head>
<body>

<div class="topbar">
    <a href="{{ route('workshop.index') }}" class="brand">3<em>x</em>30 <small>À Son Image</small></a>
    <nav>
        @auth
            @if(auth()->user()->isSeminarMember() || auth()->user()->is_admin)
                <a href="{{ route('membres.index') }}">Ressources</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="link-btn" style="color:inherit;opacity:.85;font-size:0.78rem;letter-spacing:0.06em;margin-left:1rem;text-decoration:none">Quitter</button>
            </form>
        @else
            <a href="{{ route('login') }}">Connexion</a>
        @endauth
    </nav>
</div>

<div class="install-banner" id="install-banner">
    <span>Installe 3x30 sur ton écran d'accueil.</span>
    <button type="button" id="install-button">Installer</button>
</div>

<main>
    @if(session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

@auth
@if(auth()->user()->canAccessWorkshop())
<nav class="nav-bottom">
    <a href="{{ route('workshop.index') }}" class="{{ request()->routeIs('workshop.index') ? 'active' : '' }}"><span class="ico">I</span>Aujourd'hui</a>
    <a href="{{ route('workshop.path') }}" class="{{ request()->routeIs('workshop.path', 'workshop.friction.*', 'workshop.review.*', 'workshop.memorial') ? 'active' : '' }}"><span class="ico">II</span>Chemin</a>
    <a href="{{ route('workshop.group.index') }}" class="{{ request()->routeIs('workshop.group.*') ? 'active' : '' }}"><span class="ico">III</span>Groupe</a>
    <a href="{{ route('workshop.profile') }}" class="{{ request()->routeIs('workshop.profile', 'workshop.model', 'workshop.axis', 'workshop.install') ? 'active' : '' }}"><span class="ico">IV</span>Profil</a>
</nav>
@endif
@endauth

<script>
(function () {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js', { scope: '/3x30' }).catch(function () {});
    }
    var deferred = null;
    var banner = document.getElementById('install-banner');
    var button = document.getElementById('install-button');
    var standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferred = e;
        if (!standalone && banner) { banner.style.display = 'flex'; }
    });
    if (button) {
        button.addEventListener('click', function () {
            if (!deferred) { window.location.href = '{{ route('workshop.install') }}'; return; }
            deferred.prompt();
            deferred.userChoice.then(function () { deferred = null; banner.style.display = 'none'; });
        });
    }
    window.addEventListener('appinstalled', function () { if (banner) { banner.style.display = 'none'; } });
})();
</script>
@stack('scripts')
</body>
</html>
