@extends('layouts.live')

@section('title', 'En direct')

@section('content')
    <div id="app">
        <div id="no-session" class="{{ $session ? 'hidden' : '' }}">
            <p class="eyebrow">En direct</p>
            <h1 class="h1">Pas d'atelier <em>en cours</em></h1>
            <p class="lead">Reviens sur cette page quand l'animateur aura lancé le sondage. Elle se mettra à jour toute seule.</p>
        </div>

        <div id="lobby" class="hidden">
            <p class="eyebrow" id="lobby-title"></p>
            <h1 class="h1">Tu es <em>dedans</em></h1>
            <p class="lead">Garde cette page ouverte. La première question va apparaître ici, sans rien recharger.</p>
            <div class="card quiet"><p class="muted small">Tout est anonyme : pas de nom, pas de compte, une réponse par téléphone.</p></div>
        </div>

        <div id="question" class="hidden">
            <p class="eyebrow" id="q-eyebrow"></p>
            <h1 class="h1" id="q-prompt"></h1>

            <div id="q-form">
                <div id="q-choice" class="choices hidden"></div>
                <div id="q-words" class="hidden">
                    <input class="input" id="words-input" type="text" maxlength="40" autocomplete="off" placeholder="Un mot, deux au maximum">
                </div>
                <div id="q-text" class="hidden">
                    <textarea class="input" id="text-input" maxlength="400" placeholder="Écris librement, en une ou deux phrases"></textarea>
                </div>
                <div class="btn-row"><button type="button" class="btn block" id="send">Envoyer</button></div>
                <p class="muted small" id="q-note" style="margin-top:0.6rem"></p>
            </div>

            <div id="q-done" class="hidden">
                <div class="card accent">
                    <p class="ok">Réponse enregistrée.</p>
                    <p class="muted small" id="q-mine"></p>
                    <div class="btn-row"><button type="button" class="btn secondary sm" id="edit">Modifier ma réponse</button></div>
                </div>
            </div>

            <div id="q-closed" class="hidden"><div class="card quiet"><p class="muted">Cette question est fermée. La suite arrive.</p></div></div>

            <div id="q-results" class="hidden" style="margin-top:1.2rem">
                <p class="eyebrow">Ce que la salle a répondu</p>
                <div id="results-body"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
  const $ = (id) => document.getElementById(id);
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let current = null;      // question affichee
  let editing = false;     // l'utilisateur a demande a modifier
  let lastAnswer = null;
  let pendingSend = false;
  let delay = 3000;

  function show(id, on) { $(id).classList.toggle('hidden', !on); }

  function esc(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

  function renderResults(q, r) {
    if (!r) { show('q-results', false); return; }
    let html = '';
    if (q.type === 'choice') {
      const max = Math.max(...r.items.map(i => i.count), 0);
      html = r.items.map(i => `<div class="result-row ${i.count === max && max > 0 ? 'top' : ''}"><div class="rh"><span>${esc(i.label)}</span><span class="rn">${i.count} · ${i.pct}%</span></div><div class="bar"><span style="width:${i.pct}%"></span></div></div>`).join('');
      html += `<p class="muted small">${r.total} réponse${r.total > 1 ? 's' : ''}</p>`;
    } else if (q.type === 'words') {
      const max = Math.max(...r.items.map(i => i.count), 1);
      html = '<div class="cloud">' + r.items.map(i => `<span style="font-size:${(1 + 1.6 * i.count / max).toFixed(2)}rem">${esc(i.word)}</span>`).join('') + '</div>';
    } else {
      html = '<div class="texts">' + r.items.slice(0, 30).map(v => `<div>${esc(v)}</div>`).join('') + '</div>';
    }
    $('results-body').innerHTML = html;
    show('q-results', true);
  }

  function renderQuestion(q, answer, results) {
    const changed = !current || current.id !== q.id;
    if (changed) { editing = false; }
    current = q;
    show('no-session', false); show('lobby', false); show('question', true);
    $('q-eyebrow').textContent = 'Question ' + q.position;
    $('q-prompt').textContent = q.prompt;

    if (changed) {
      $('q-choice').innerHTML = (q.options || []).map((o, i) => `<label class="choice"><input type="radio" name="opt" value="${i}"> <span>${esc(o)}</span></label>`).join('');
      $('words-input').value = ''; $('text-input').value = '';
    }
    show('q-choice', q.type === 'choice'); show('q-words', q.type === 'words'); show('q-text', q.type === 'text');
    $('q-note').textContent = q.type === 'words' ? 'Un seul mot, celui qui te vient.' : (q.type === 'choice' ? 'Une seule réponse.' : '');

    const open = q.status === 'open';
    const answered = !!answer && !editing;
    show('q-form', open && !answered);
    show('q-done', answered && open);
    show('q-closed', !open && !results);
    if (answer) {
      lastAnswer = answer;
      $('q-mine').textContent = answer.option_index !== null && q.options ? q.options[answer.option_index] : (answer.value || '');
    }
    renderResults(q, results);
  }

  async function poll() {
    if (pendingSend) { return schedule(); }
    try {
      const r = await fetch('/live/etat', { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
      const s = await r.json();
      delay = 3000;
      if (!s.session) { show('no-session', true); show('lobby', false); show('question', false); current = null; }
      else if (!s.question) { $('lobby-title').textContent = s.session.title; show('no-session', false); show('lobby', true); show('question', false); current = null; }
      else { renderQuestion(s.question, s.answer, s.results); }
    } catch (e) { delay = Math.min(delay * 2, 15000); }
    schedule();
  }
  function schedule() { setTimeout(poll, delay); }

  $('send').addEventListener('click', async () => {
    if (!current) return;
    const body = { question_id: current.id };
    if (current.type === 'choice') {
      const checked = document.querySelector('input[name="opt"]:checked');
      if (!checked) { $('q-note').textContent = 'Choisis une réponse.'; return; }
      body.option_index = parseInt(checked.value, 10);
    } else {
      body.value = (current.type === 'words' ? $('words-input') : $('text-input')).value.trim();
      if (!body.value) { $('q-note').textContent = 'Écris quelque chose avant d\'envoyer.'; return; }
    }
    pendingSend = true;
    $('send').disabled = true;
    try {
      const r = await fetch('/live/repondre', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(body) });
      if (r.ok) { editing = false; renderQuestion(current, { option_index: body.option_index ?? null, value: body.value ?? null }, null); }
      else if (r.status === 409) { $('q-note').textContent = 'Trop tard, la question vient de se fermer.'; }
      else { $('q-note').textContent = 'Réponse refusée, réessaie.'; }
    } catch (e) { $('q-note').textContent = 'Pas de réseau, réessaie.'; }
    $('send').disabled = false;
    pendingSend = false;
  });

  $('edit').addEventListener('click', () => { editing = true; if (current) renderQuestion(current, lastAnswer, null); });

  poll();
})();
</script>
@endpush
