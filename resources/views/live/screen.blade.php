@extends('layouts.live')

@section('title', 'Écran')

@section('no-topbar', '1')

@section('main-class', 'wide screen')

@push('head')
<style>
body { background: var(--forest); color: var(--cream); }
main.screen { max-width: 1400px; padding: 3vh 5vw; display: flex; flex-direction: column; min-height: 100vh; }
.scr-head { display: flex; justify-content: space-between; align-items: baseline; gap: 2rem; font-size: 1.1rem; opacity: 0.8; margin-bottom: 2vh; }
.scr-head .brand { font-family: 'Cormorant Garamond', serif; font-size: 1.6rem; }
.scr-head .brand em { color: var(--gold); font-style: italic; }
.scr-body { flex: 1; display: flex; flex-direction: column; justify-content: center; }
.join { display: grid; grid-template-columns: 1fr auto; gap: 4vw; align-items: center; }
.join .url { font-family: 'Cormorant Garamond', serif; font-size: clamp(2.4rem, 6vw, 5rem); line-height: 1.05; color: var(--cream); }
.join .url em { color: var(--gold); font-style: normal; }
.join .hint { font-size: clamp(1rem, 2vw, 1.5rem); opacity: 0.75; margin-top: 1.5rem; font-weight: 300; }
.join #qr { background: var(--cream); padding: 1.2vw; width: min(34vh, 32vw, 100%); box-sizing: content-box; }
.join #qr canvas { display: none !important; }
.join #qr img { display: block !important; width: 100% !important; height: auto !important; }
@media (max-width: 800px) { .join { grid-template-columns: 1fr; } .join #qr { width: min(50vw, 260px); } }
.q-eyebrow { font-size: clamp(0.8rem, 1.4vw, 1.1rem); letter-spacing: 0.25em; text-transform: uppercase; color: var(--gold); margin-bottom: 1.5vh; }
.q-prompt { font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 4.6vw, 4.4rem); line-height: 1.12; font-weight: 400; margin-bottom: 3vh; }
.q-count { font-size: clamp(1rem, 1.8vw, 1.5rem); opacity: 0.75; font-weight: 300; }
.q-count strong { font-family: 'Cormorant Garamond', serif; font-size: 1.6em; color: var(--gold); font-weight: 500; }
.scr-results .result-row { margin-bottom: 2vh; }
.scr-results .rh { font-size: clamp(1.1rem, 2.4vw, 2.2rem); }
.scr-results .rn { font-size: 0.7em; opacity: 0.8; color: var(--cream); }
.scr-results .bar { height: clamp(14px, 2.4vh, 26px); background: rgba(255,255,255,0.12); }
.scr-results .bar > span { background: var(--earth-light); }
.scr-results .result-row.top .bar > span { background: var(--gold); }
.scr-results .cloud { gap: 0.6vh 2.4vw; padding: 2vh 0; }
.scr-results .cloud span { color: var(--cream); }
.scr-results .cloud span:nth-child(3n+1) { color: var(--gold-light); }
.scr-results .cloud span:nth-child(4n+2) { color: var(--sand-dark); }
.scr-results .texts { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.2vh 1.2vw; }
.scr-results .texts div { background: rgba(255,255,255,0.08); color: var(--cream); font-size: clamp(1.1rem, 1.9vw, 1.7rem); padding: 1.4vh 1.4vw; }
.scr-foot { font-size: clamp(0.85rem, 1.3vw, 1.1rem); opacity: 0.55; margin-top: 3vh; }
.scr-foot em { color: var(--gold); font-style: normal; }
</style>
@endpush

@section('content')
    <div class="scr-head">
        <span class="brand">À Son <em>Image</em></span>
        <span id="scr-title">{{ $session?->title }}</span>
    </div>

    <div class="scr-body">
        <div id="scr-none" class="{{ $session ? 'hidden' : '' }}">
            <p class="q-prompt">Pas d'atelier en cours.</p>
            <p class="q-count">Active un sondage depuis <a href="/sondage" style="color:var(--gold)">/sondage</a>.</p>
        </div>

        <div id="scr-join" class="join hidden">
            <div>
                <p class="q-eyebrow">Rejoins le sondage</p>
                <p class="url">Sur ton téléphone<br><em id="scr-url"></em></p>
                <p class="hint">Ou scanne le code. Pas de compte, pas de nom, tout est anonyme.</p>
            </div>
            <div id="qr"></div>
        </div>

        <div id="scr-question" class="hidden">
            <p class="q-eyebrow" id="scr-eyebrow"></p>
            <p class="q-prompt" id="scr-prompt"></p>
            <p class="q-count" id="scr-count"></p>
            <div class="scr-results" id="scr-results"></div>
        </div>
    </div>

    <p class="scr-foot" id="scr-foot"></p>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha384-3zSEDfvllQohrq0PHL1fOXJuC/jSOO34H46t6UQfobFOmxE5BpjjaIJY5F2/bMnU" crossorigin="anonymous"></script>
<script>
(function () {
  const $ = (id) => document.getElementById(id);
  let qrDrawn = null;
  let lastQuestion = null;
  let delay = 2000;

  function show(id, on) { $(id).classList.toggle('hidden', !on); }
  function esc(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

  function drawQr(url) {
    if (qrDrawn === url || typeof QRCode === 'undefined') return;
    qrDrawn = url;
    $('qr').innerHTML = '';
    new QRCode($('qr'), { text: url, width: 480, height: 480, colorDark: '#2A2A25', colorLight: '#FFFDF7', correctLevel: QRCode.CorrectLevel.M });
  }

  function renderResults(q, r) {
    if (!r) { $('scr-results').innerHTML = ''; return; }
    let html = '';
    if (q.type === 'choice') {
      const max = Math.max(...r.items.map(i => i.count), 0);
      html = r.items.map(i => `<div class="result-row ${i.count === max && max > 0 ? 'top' : ''}"><div class="rh"><span>${esc(i.label)}</span><span class="rn">${i.count} · ${i.pct}%</span></div><div class="bar"><span style="width:${i.pct}%"></span></div></div>`).join('');
    } else if (q.type === 'words') {
      const max = Math.max(...r.items.map(i => i.count), 1);
      html = '<div class="cloud">' + r.items.map(i => `<span style="font-size:${(1.6 + 4.4 * i.count / max).toFixed(2)}vw">${esc(i.word)}</span>`).join('') + '</div>';
    } else {
      html = '<div class="texts">' + r.items.slice(0, 12).map(v => `<div>${esc(v)}</div>`).join('') + '</div>';
    }
    $('scr-results').innerHTML = html;
  }

  async function poll() {
    try {
      const r = await fetch('/live/ecran/etat', { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
      const s = await r.json();
      delay = 2000;
      if (!s.session) { show('scr-none', true); show('scr-join', false); show('scr-question', false); $('scr-foot').textContent = ''; }
      else {
        $('scr-title').textContent = s.session.title;
        $('scr-foot').innerHTML = `Réponds sur <em>${esc(s.session.short_url)}</em>`;
        show('scr-none', false);
        if (!s.question) {
          show('scr-join', true); show('scr-question', false);
          $('scr-url').textContent = s.session.short_url;
          drawQr(s.session.join_url);
        } else {
          show('scr-join', false); show('scr-question', true);
          const q = s.question;
          $('scr-eyebrow').textContent = 'Question ' + q.position + (q.status === 'open' ? ' · ouverte' : ' · fermée');
          $('scr-prompt').textContent = q.prompt;
          $('scr-count').innerHTML = `<strong>${s.count}</strong> réponse${s.count > 1 ? 's' : ''}`;
          if (!lastQuestion || lastQuestion !== q.id) { $('scr-results').innerHTML = ''; lastQuestion = q.id; }
          renderResults(q, s.results);
        }
      }
    } catch (e) { delay = Math.min(delay * 2, 10000); }
    setTimeout(poll, delay);
  }
  poll();
})();
</script>
@endpush
