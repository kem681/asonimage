@extends('layouts.live')

@section('title', 'Présentateur · '.$session->title)

@section('nav')
    <a href="{{ route('admin.live.show', $session) }}">Réglages</a>
    <a href="{{ route('live.screen') }}" target="_blank">Ouvrir l'écran</a>
@endsection

@section('main-class', 'wide presenter')

@push('head')
<style>
main.presenter { max-width: 1240px; }
.pres { display: grid; grid-template-columns: minmax(0, 3fr) minmax(260px, 2fr); gap: 1.4rem; align-items: start; }
@media (max-width: 860px) { .pres { grid-template-columns: 1fr; } }
.pres-main .card { padding: 1.4rem 1.4rem; }
.pres-prompt { font-family: 'Cormorant Garamond', serif; font-size: clamp(1.5rem, 2.6vw, 2.2rem); line-height: 1.15; margin: 0.3rem 0 0.8rem; }
.pres-count { display: flex; gap: 0.6rem; align-items: baseline; margin-bottom: 0.8rem; }
.pres-count .count { font-size: 2rem; }
.pres-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.5rem; margin-top: 1rem; }
.pres-actions form { display: contents; }
.pres-actions .btn { padding: 1rem 0.8rem; }
.notes { white-space: pre-line; background: var(--sand); border-left: 3px solid var(--gold); padding: 0.9rem 1rem; font-size: 0.95rem; line-height: 1.5; margin-top: 1rem; }
.notes .eyebrow { margin-bottom: 0.3rem; }
.qlist { list-style: none; }
.qlist li { border: 1px solid var(--sand-dark); background: var(--cream); margin-bottom: 0.5rem; padding: 0.75rem 0.85rem; display: grid; grid-template-columns: 1fr auto; gap: 0.5rem 0.8rem; align-items: center; }
.qlist li.current { border-color: var(--gold); box-shadow: 0 0 0 2px var(--gold-light); }
.qlist .qp { font-size: 0.92rem; line-height: 1.3; }
.qlist .qm { font-size: 0.72rem; color: var(--ink-soft); margin-top: 0.2rem; }
.qlist form { display: inline; }
.qlist .btn { padding: 0.5rem 0.75rem; font-size: 0.66rem; white-space: nowrap; }
.pres-results { margin-top: 0.4rem; }
.pres-results .cloud span { font-family: 'Cormorant Garamond', serif; }
.pres-results .texts div { font-size: 1.05rem; }
.hint-room { font-size: 0.78rem; color: var(--ink-soft); margin-top: 0.4rem; }
</style>
@endpush

@section('content')
    @php $current = $session->current_question_id ? $questions->firstWhere('id', $session->current_question_id) : null; @endphp

    <div style="display:flex; justify-content:space-between; gap:1rem; align-items:baseline; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <p class="eyebrow" style="margin:0">Présentateur · code {{ $session->code }} · <span id="online">{{ $session->is_active ? 'en ligne sur /live' : 'hors ligne' }}</span></p>
            <h1 class="h2" style="margin:0">{{ $session->title }}</h1>
        </div>
        <div class="btn-row" style="margin:0">
            @unless ($session->is_active)
                <form method="POST" action="{{ route('admin.live.activate', $session) }}">@csrf<button type="submit" class="btn gold sm">Mettre en ligne</button></form>
            @endunless
            <form method="POST" action="{{ route('admin.live.lobby', $session) }}">@csrf<button type="submit" class="btn secondary sm" {{ $session->current_question_id ? '' : 'disabled' }}>Écran d'attente (QR)</button></form>
        </div>
    </div>

    <div class="pres">
        <div class="pres-main">
            <div class="card accent" id="pres-current" data-id="{{ $current?->id }}">
                @if ($current)
                    <p class="eyebrow">Question {{ $current->position }} sur {{ $questions->count() }} · {{ $current->typeLabel() }} · <span id="pres-status">{{ ['pending' => 'en attente', 'open' => 'ouverte', 'closed' => 'fermée'][$current->status] }}</span><span id="pres-shown">{{ $current->show_results ? ' · résultats affichés à la salle' : '' }}</span></p>
                    <p class="pres-prompt" id="pres-prompt">{{ $current->prompt }}</p>
                    <div class="pres-count"><span class="count" id="pres-count">{{ $current->answers_count }}</span><span class="muted small">réponse(s)</span></div>
                    <div class="pres-results" id="pres-results"></div>
                    <p class="hint-room" id="pres-hint">{{ $current->show_results ? 'La salle et l\'écran voient ces résultats.' : 'Toi seul vois ces résultats pour l\'instant.' }}</p>

                    <div class="pres-actions">
                        @if ($current->status !== 'open')
                            <form method="POST" action="{{ route('admin.live.questions.open', [$session, $current]) }}">@csrf<button type="submit" class="btn gold">{{ $current->status === 'closed' ? 'Rouvrir' : 'Ouvrir' }}</button></form>
                        @else
                            <form method="POST" action="{{ route('admin.live.questions.close', [$session, $current]) }}">@csrf<button type="submit" class="btn">Fermer</button></form>
                        @endif
                        <form method="POST" action="{{ route('admin.live.questions.results', [$session, $current]) }}">@csrf<button type="submit" class="btn secondary">{{ $current->show_results ? 'Masquer à la salle' : 'Afficher à la salle' }}</button></form>
                        @php $next = $questions->firstWhere('position', '>', $current->position); @endphp
                        @if ($next)
                            <form method="POST" action="{{ route('admin.live.questions.open', [$session, $next]) }}">@csrf<button type="submit" class="btn gold">Suivante : ouvrir Q{{ $next->position }}</button></form>
                        @endif
                    </div>

                    @if ($current->notes)
                        <div class="notes"><p class="eyebrow">Mes notes</p>{{ $current->notes }}</div>
                    @endif
                @else
                    <p class="eyebrow">Écran d'attente</p>
                    <p class="pres-prompt">L'écran montre le QR et l'adresse. Ouvre la première question quand la salle est prête.</p>
                    @if ($questions->isNotEmpty())
                        <div class="pres-actions">
                            <form method="POST" action="{{ route('admin.live.questions.open', [$session, $questions->first()]) }}">@csrf<button type="submit" class="btn gold">Ouvrir Q1</button></form>
                        </div>
                        @if ($questions->first()->notes)
                            <div class="notes"><p class="eyebrow">Notes de Q1</p>{{ $questions->first()->notes }}</div>
                        @endif
                    @endif
                @endif
            </div>
        </div>

        <div class="pres-side">
            <p class="eyebrow">Toutes les questions</p>
            <ul class="qlist">
                @foreach ($questions as $q)
                    <li class="{{ $current && $current->id === $q->id ? 'current' : '' }}" data-id="{{ $q->id }}">
                        <div>
                            <div class="qp"><strong>Q{{ $q->position }}</strong> {{ $q->prompt }}</div>
                            <div class="qm"><span class="js-status">{{ ['pending' => 'En attente', 'open' => 'Ouverte', 'closed' => 'Fermée'][$q->status] }}</span> · <span class="js-count">{{ $q->answers_count }}</span> rép.</div>
                        </div>
                        <form method="POST" action="{{ route('admin.live.questions.open', [$session, $q]) }}">@csrf<button type="submit" class="btn {{ $q->status === 'open' ? 'secondary' : '' }} sm">{{ $q->status === 'open' ? 'En cours' : 'Ouvrir' }}</button></form>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
  const $ = (id) => document.getElementById(id);
  const labels = { pending: 'En attente', open: 'Ouverte', closed: 'Fermée' };
  const labelsLow = { pending: 'en attente', open: 'ouverte', closed: 'fermée' };
  const startId = $('pres-current').dataset.id || '';
  function esc(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

  function renderResults(q, r) {
    const box = $('pres-results');
    if (!box || !r) return;
    let html = '';
    if (q.type === 'choice') {
      const max = Math.max(...r.items.map(i => i.count), 0);
      html = r.items.map(i => `<div class="result-row ${i.count === max && max > 0 ? 'top' : ''}"><div class="rh"><span>${esc(i.label)}</span><span class="rn">${i.count} · ${i.pct}%</span></div><div class="bar"><span style="width:${i.pct}%"></span></div></div>`).join('');
    } else if (q.type === 'words') {
      const max = Math.max(...r.items.map(i => i.count), 1);
      html = '<div class="cloud">' + r.items.map(i => `<span style="font-size:${(1 + 1.8 * i.count / max).toFixed(2)}rem">${esc(i.word)}</span>`).join('') + '</div>';
    } else {
      html = '<div class="texts">' + r.items.slice(0, 40).map(v => `<div>${esc(v)}</div>`).join('') + '</div>';
    }
    box.innerHTML = html;
  }

  async function poll() {
    try {
      const r = await fetch('{{ route('admin.live.state', $session) }}', { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
      if (!r.ok) { throw new Error(r.status); }
      const s = await r.json();
      // La question courante a change depuis un autre appareil : on recharge pour avoir les bons boutons.
      if (String(s.current_question_id ?? '') !== startId) { location.reload(); return; }
      for (const q of s.questions) {
        const li = document.querySelector(`.qlist li[data-id="${q.id}"]`);
        if (!li) continue;
        li.querySelector('.js-status').textContent = labels[q.status];
        li.querySelector('.js-count').textContent = q.answers;
      }
      if (s.current) {
        $('pres-count').textContent = s.current.results.total;
        $('pres-status').textContent = labelsLow[s.current.status];
        $('pres-shown').textContent = s.current.show_results ? ' · résultats affichés à la salle' : '';
        $('pres-hint').textContent = s.current.show_results ? 'La salle et l\'écran voient ces résultats.' : 'Toi seul vois ces résultats pour l\'instant.';
        renderResults(s.current, s.current.results);
      }
    } catch (e) {}
    setTimeout(poll, 2500);
  }
  poll();
})();
</script>
@endpush
