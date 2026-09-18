<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'En direct') — À Son Image</title>
<meta name="theme-color" content="#3A4A3A">
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

.topbar { background: var(--forest); color: var(--cream); padding: calc(0.8rem + env(safe-area-inset-top)) 1.2rem 0.8rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
.topbar a { color: inherit; text-decoration: none; }
.topbar .brand { font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; font-weight: 500; }
.topbar .brand em { color: var(--gold); font-style: italic; }
.topbar nav { display: flex; gap: 1rem; align-items: center; }
.topbar nav a, .topbar nav button { font-size: 0.75rem; letter-spacing: 0.06em; opacity: 0.85; background: none; border: none; color: inherit; font-family: inherit; cursor: pointer; }

main { flex: 1; width: 100%; max-width: 640px; margin: 0 auto; padding: 1.4rem 1.2rem calc(2.5rem + env(safe-area-inset-bottom)); }
main.wide { max-width: 900px; }

.status { background: var(--sand); border-left: 3px solid var(--gold); padding: 0.9rem 1.1rem; margin-bottom: 1.4rem; font-size: 0.92rem; }
.errors { background: #fbeceb; border-left: 3px solid var(--danger); padding: 0.9rem 1.1rem; margin-bottom: 1.4rem; font-size: 0.92rem; color: #7a2b26; }
.errors ul { margin-left: 1.1rem; }

.eyebrow { font-size: 0.7rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--earth-light); margin-bottom: 0.4rem; }
.h1 { font-family: 'Cormorant Garamond', serif; font-size: clamp(1.7rem, 6vw, 2.3rem); font-weight: 400; line-height: 1.15; margin-bottom: 0.6rem; }
.h1 em { font-style: italic; color: var(--earth); }
.h2 { font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; font-weight: 500; margin-bottom: 0.5rem; }
.lead { color: var(--ink-soft); font-weight: 300; margin-bottom: 1.4rem; }
.muted { color: var(--ink-soft); font-weight: 300; font-size: 0.9rem; }
.small { font-size: 0.82rem; }
p + p { margin-top: 0.8rem; }

.card { background: var(--cream); border: 1px solid var(--sand-dark); padding: 1.2rem 1.15rem; margin-bottom: 1rem; position: relative; }
.card.accent { border-top: 3px solid var(--gold); }
.card.quiet { background: var(--sand); border-color: transparent; }
.card.current { border-color: var(--gold); box-shadow: 0 0 0 2px var(--gold-light); }
.card-title { font-family: 'Cormorant Garamond', serif; font-size: 1.35rem; margin-bottom: 0.4rem; line-height: 1.2; }

.btn { display: inline-block; padding: 0.85rem 1.4rem; background: var(--forest); color: var(--cream); border: none; font-family: inherit; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; cursor: pointer; text-decoration: none; text-align: center; }
.btn:hover { background: var(--ink); }
.btn.block { display: block; width: 100%; }
.btn.secondary { background: transparent; color: var(--forest); border: 1px solid var(--forest); }
.btn.gold { background: var(--gold); color: var(--ink); }
.btn.danger { background: transparent; color: var(--danger); border: 1px solid var(--danger); }
.btn.sm { padding: 0.55rem 0.9rem; font-size: 0.7rem; }
.btn[disabled] { opacity: 0.5; cursor: default; }
.btn-row { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.8rem; }
.btn-row form { display: inline; }

form.stack { display: flex; flex-direction: column; gap: 1rem; }
form.stack label { font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--earth); font-weight: 500; margin-bottom: 0.35rem; display: block; }
form.stack input, form.stack select, form.stack textarea, .input { width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--sand-dark); background: var(--cream); font-family: inherit; font-size: 1rem; color: var(--ink); outline: none; border-radius: 0; }
form.stack textarea, textarea.input { min-height: 5rem; resize: vertical; }
form.stack input:focus, form.stack textarea:focus, form.stack select:focus, .input:focus { border-color: var(--gold); }
form.stack .hint { font-size: 0.82rem; color: var(--ink-soft); font-weight: 300; margin-top: 0.35rem; }

.choices { display: flex; flex-direction: column; gap: 0.5rem; }
.choice { display: flex; align-items: center; gap: 0.7rem; padding: 1rem 0.95rem; border: 1px solid var(--sand-dark); background: var(--cream); cursor: pointer; font-size: 1.02rem; }
.choice input { accent-color: var(--forest); flex: none; width: 1.1rem; height: 1.1rem; }
.choice:has(input:checked) { border-color: var(--gold); background: var(--sand); }

.result-row { margin-bottom: 0.8rem; }
.result-row .rh { display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; margin-bottom: 0.3rem; }
.result-row .rn { font-size: 0.8rem; color: var(--ink-soft); white-space: nowrap; }
.bar { height: 12px; background: var(--sand-dark); overflow: hidden; }
.bar > span { display: block; height: 100%; background: var(--earth-light); transition: width .5s ease; }
.result-row.top .bar > span { background: var(--gold); }

.cloud { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 0.3rem 0.9rem; padding: 0.6rem 0; line-height: 1.1; }
.cloud span { font-family: 'Cormorant Garamond', serif; color: var(--forest); transition: font-size .4s ease; }
.cloud span:nth-child(3n+1) { color: var(--earth); }
.cloud span:nth-child(4n+2) { color: var(--gold); }

.texts { display: flex; flex-direction: column; gap: 0.5rem; }
.texts div { background: var(--sand); padding: 0.7rem 0.9rem; font-family: 'Cormorant Garamond', serif; font-size: 1.15rem; line-height: 1.3; }

.tag { display: inline-block; font-size: 0.64rem; letter-spacing: 0.14em; text-transform: uppercase; padding: 0.2rem 0.55rem; background: var(--sand-dark); color: var(--ink-soft); vertical-align: middle; }
.tag.open { background: var(--gold); color: var(--ink); }
.tag.closed { background: var(--forest); color: var(--cream); }
.tag.results { background: var(--earth); color: var(--cream); }

.code-big { font-family: 'Cormorant Garamond', serif; font-size: 2.4rem; letter-spacing: 0.25em; text-align: center; padding: 0.8rem; background: var(--sand); margin: 0.6rem 0; user-select: all; }
.list { list-style: none; }
.list li { padding: 0.8rem 0; border-bottom: 1px solid var(--sand-dark); }
.list li:last-child { border-bottom: none; }
.count { font-family: 'Cormorant Garamond', serif; font-size: 1.6rem; color: var(--earth); }
.ok { color: var(--forest); font-weight: 500; }
.hidden { display: none !important; }

@media (min-width: 720px) { main { padding-top: 2.4rem; } }
</style>
@stack('head')
</head>
<body>
@unless (View::hasSection('no-topbar'))
<div class="topbar">
    <a href="/" class="brand">À Son <em>Image</em></a>
    <nav>@yield('nav')</nav>
</div>
@endunless

<main class="@yield('main-class')">
    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
