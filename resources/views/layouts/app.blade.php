<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Espace membre') — À Son Image</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<style>
:root {
  --sand: #F5F0E8; --sand-dark: #E8E0D0; --earth: #8B7355; --earth-light: #A89070;
  --forest: #3A4A3A; --forest-light: #4A5E4A; --gold: #C4A35A; --gold-light: #D4B86A;
  --cream: #FFFDF7; --ink: #2A2A25; --ink-soft: #4A4A42; --warm-white: #FAF8F3;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DM Sans', sans-serif; background: var(--warm-white); color: var(--ink); min-height: 100vh; display: flex; flex-direction: column; }

.topbar { background: var(--forest); color: var(--cream); padding: 1.1rem 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
.topbar a { color: inherit; text-decoration: none; }
.topbar .brand { font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; font-weight: 500; }
.topbar .brand em { color: var(--gold); font-style: italic; }
.topbar nav { display: flex; align-items: center; gap: 1.6rem; font-size: 0.85rem; letter-spacing: 0.04em; }
.topbar nav a { opacity: 0.85; transition: opacity .2s ease; }
.topbar nav a:hover { opacity: 1; }
.topbar form { display: inline; }
.topbar button.link { background: none; border: none; color: inherit; font: inherit; letter-spacing: inherit; opacity: 0.85; cursor: pointer; padding: 0; }
.topbar button.link:hover { opacity: 1; }

main { flex: 1; padding: 3rem 2rem; max-width: 1080px; margin: 0 auto; width: 100%; }

.status { background: var(--sand); border-left: 3px solid var(--gold); padding: 1rem 1.4rem; margin-bottom: 2rem; font-size: 0.92rem; }
.errors { background: #fbeceb; border-left: 3px solid #b3413a; padding: 1rem 1.4rem; margin-bottom: 2rem; font-size: 0.92rem; color: #7a2b26; }
.errors ul { margin-left: 1.2rem; }

.page-title { font-family: 'Cormorant Garamond', serif; font-size: clamp(1.8rem, 3.5vw, 2.6rem); font-weight: 400; margin-bottom: 0.6rem; color: var(--ink); }
.page-title em { font-style: italic; color: var(--earth); }
.page-sub { color: var(--ink-soft); font-weight: 300; margin-bottom: 2.5rem; }

.breadcrumb { font-size: 0.78rem; letter-spacing: 0.08em; text-transform: uppercase; color: var(--earth-light); margin-bottom: 1rem; }
.breadcrumb a { color: var(--earth); text-decoration: none; }

.card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 1.2rem; }
.card { display: block; background: var(--cream); border: 1px solid var(--sand-dark); padding: 1.8rem 1.6rem; text-decoration: none; color: var(--ink); transition: all .25s ease; position: relative; overflow: hidden; }
.card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(90deg, var(--gold), var(--earth-light)); transform: scaleX(0); transform-origin: left; transition: transform .3s ease; }
.card:hover::before { transform: scaleX(1); }
.card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
.card .card-eyebrow { font-size: 0.7rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--earth-light); margin-bottom: 0.6rem; }
.card .card-title { font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: var(--ink); margin-bottom: 0.5rem; }
.card .card-meta { font-size: 0.82rem; color: var(--ink-soft); font-weight: 300; }
.card .card-count { display: inline-block; margin-top: 0.8rem; font-size: 0.75rem; letter-spacing: 0.05em; color: var(--gold); font-weight: 600; }

.empty-state { padding: 3rem 2rem; text-align: center; background: var(--cream); border: 1px dashed var(--sand-dark); color: var(--ink-soft); font-weight: 300; }

form.stack { display: flex; flex-direction: column; gap: 1.1rem; max-width: 460px; }
form.stack label { font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--earth); font-weight: 500; margin-bottom: 0.3rem; display: block; }
form.stack input, form.stack select, form.stack textarea { width: 100%; padding: 0.85rem 1.1rem; border: 1px solid var(--sand-dark); background: var(--cream); font-family: inherit; font-size: 0.95rem; color: var(--ink); outline: none; }
form.stack input:focus, form.stack select:focus, form.stack textarea:focus { border-color: var(--gold); }
form.stack .btn { align-self: flex-start; margin-top: 0.6rem; padding: 0.95rem 2.4rem; background: var(--forest); color: var(--cream); border: none; font-family: inherit; font-size: 0.85rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; cursor: pointer; transition: all .3s ease; }
form.stack .btn:hover { background: var(--ink); }

table.data { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
table.data th, table.data td { text-align: left; padding: 0.7rem 0.9rem; border-bottom: 1px solid var(--sand-dark); }
table.data th { font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase; color: var(--earth); }

.resource-row { display: flex; align-items: center; justify-content: space-between; background: var(--cream); border: 1px solid var(--sand-dark); padding: 1.2rem 1.5rem; margin-bottom: 0.8rem; }
.resource-row .title { font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; }
.resource-row .desc { font-size: 0.85rem; color: var(--ink-soft); font-weight: 300; margin-top: 0.2rem; }
.resource-row a.download { color: var(--forest); font-weight: 600; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase; text-decoration: none; white-space: nowrap; margin-left: 1.5rem; }
.resource-row a.download:hover { color: var(--gold); }

.pdf-viewer { border: 1px solid var(--sand-dark); background: var(--sand); height: 80vh; overflow-y: auto; padding: 1rem; }
.pdf-viewer .pdf-page { display: block; width: 100%; height: auto; margin: 0 auto 0.8rem; box-shadow: 0 4px 16px rgba(0,0,0,0.08); user-select: none; -webkit-user-select: none; }
.pdf-viewer .pdf-page:last-child { margin-bottom: 0; }
.pdf-viewer .pdf-loading { text-align: center; color: var(--ink-soft); font-size: 0.9rem; padding: 2rem; }

audio { accent-color: var(--forest); }

footer { text-align: center; padding: 2rem; font-size: 0.75rem; color: var(--ink-soft); }
</style>
</head>
<body>

<div class="topbar">
    <a href="{{ route('landing') }}" class="brand">À Son <em>Image</em></a>
    <nav>
        @auth
            <a href="{{ route('membres.index') }}">Ressources</a>
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.resources.index') }}">Admin ressources</a>
                <a href="{{ route('admin.emails.index') }}">Admin emails</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="link">Se déconnecter</button>
            </form>
        @else
            <a href="{{ route('login') }}">Connexion</a>
            <a href="{{ route('register') }}">Créer un compte</a>
        @endauth
    </nav>
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

<footer>À Son Image · Espace membre</footer>

</body>
</html>
