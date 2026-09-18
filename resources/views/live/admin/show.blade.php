@extends('layouts.live')

@section('title', $session->title)

@section('nav')
    <a href="{{ route('admin.live.index') }}">Sondages</a>
    <a href="{{ route('live.screen') }}" target="_blank">Écran</a>
@endsection

@section('content')
    <p class="eyebrow">Sondage · code {{ $session->code }}</p>
    <h1 class="h1">{{ $session->title }}</h1>

    <div class="card {{ $session->is_active ? 'accent' : 'quiet' }}">
        @if ($session->is_active)
            <p><span class="tag open">En ligne</span> La salle répond sur <strong>{{ $session->shortJoinUrl() }}</strong>, l'écran est sur <strong>{{ $session->shortJoinUrl() }}/ecran</strong>.</p>
        @else
            <p><span class="tag">Hors ligne</span> Mets-le en ligne avant l'atelier : /live affichera ce sondage.</p>
        @endif
        <div class="btn-row">
            <form method="POST" action="{{ route('admin.live.activate', $session) }}">@csrf<button type="submit" class="btn {{ $session->is_active ? 'secondary' : 'gold' }} sm">{{ $session->is_active ? 'Mettre en pause' : 'Mettre en ligne' }}</button></form>
            <form method="POST" action="{{ route('admin.live.lobby', $session) }}">@csrf<button type="submit" class="btn secondary sm" {{ $session->current_question_id ? '' : 'disabled' }}>Écran d'attente (QR)</button></form>
            <a class="btn sm" href="{{ route('admin.live.presenter', $session) }}">Vue présentateur</a>
            <a class="btn secondary sm" href="{{ route('admin.live.export', $session) }}">Export CSV</a>
        </div>
    </div>

    <div id="questions">
    @foreach ($questions as $q)
        <div class="card {{ $session->current_question_id === $q->id ? 'current' : '' }}" data-id="{{ $q->id }}">
            <div style="display:flex; justify-content:space-between; gap:0.8rem; align-items:baseline;">
                <p class="eyebrow" style="margin:0">Q{{ $q->position }} · {{ $q->typeLabel() }}</p>
                <span>
                    <span class="tag js-status {{ $q->status }}">{{ ['pending' => 'En attente', 'open' => 'Ouverte', 'closed' => 'Fermée'][$q->status] }}</span>
                    <span class="tag results js-results {{ $q->show_results ? '' : 'hidden' }}">Résultats affichés</span>
                </span>
            </div>
            <p class="card-title" style="margin-top:0.4rem">{{ $q->prompt }}</p>
            @if ($q->type === 'choice')
                <p class="muted small">{{ implode(' · ', $q->options ?? []) }}</p>
            @endif
            <p style="margin-top:0.5rem"><span class="count js-count">{{ $q->answers_count }}</span> <span class="muted small">réponse(s)</span></p>

            <div class="btn-row">
                @if ($q->status !== 'open')
                    <form method="POST" action="{{ route('admin.live.questions.open', [$session, $q]) }}">@csrf<button type="submit" class="btn gold sm">{{ $q->status === 'closed' ? 'Rouvrir' : 'Ouvrir' }}</button></form>
                @else
                    <form method="POST" action="{{ route('admin.live.questions.close', [$session, $q]) }}">@csrf<button type="submit" class="btn sm">Fermer</button></form>
                @endif
                <form method="POST" action="{{ route('admin.live.questions.results', [$session, $q]) }}">@csrf<button type="submit" class="btn secondary sm">{{ $q->show_results ? 'Masquer les résultats' : 'Afficher les résultats' }}</button></form>
                @if ($q->answers_count > 0)
                    <form method="POST" action="{{ route('admin.live.questions.reset', [$session, $q]) }}" onsubmit="return confirm('Effacer les {{ $q->answers_count }} réponses de cette question ?')">@csrf<button type="submit" class="btn danger sm">Effacer</button></form>
                @else
                    <form method="POST" action="{{ route('admin.live.questions.destroy', [$session, $q]) }}" onsubmit="return confirm('Supprimer cette question ?')">@csrf @method('DELETE')<button type="submit" class="btn danger sm">Supprimer</button></form>
                @endif
            </div>
        </div>
    @endforeach
    </div>

    <div class="card quiet" style="margin-top:2rem">
        <p class="eyebrow">Ajouter une question</p>
        <form class="stack" method="POST" action="{{ route('admin.live.questions.store', $session) }}">
            @csrf
            <div>
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="choice">Choix unique</option>
                    <option value="words">Nuage de mots</option>
                    <option value="text">Texte libre</option>
                </select>
            </div>
            <div>
                <label for="prompt">Question</label>
                <textarea id="prompt" name="prompt" required maxlength="500">{{ old('prompt') }}</textarea>
            </div>
            <div>
                <label for="options">Options (choix unique, une par ligne)</label>
                <textarea id="options" name="options" maxlength="2000">{{ old('options') }}</textarea>
            </div>
            <button type="submit" class="btn secondary">Ajouter</button>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.live.destroy', $session) }}" onsubmit="return confirm('Supprimer ce sondage et toutes ses réponses ?')" style="margin-top:1.5rem">
        @csrf @method('DELETE')
        <button type="submit" class="btn danger sm">Supprimer le sondage</button>
    </form>
@endsection

@push('scripts')
<script>
(function () {
  // Rafraichit compteurs et etats sans recharger, pour suivre les reponses arriver depuis le telephone.
  const labels = { pending: 'En attente', open: 'Ouverte', closed: 'Fermée' };
  async function poll() {
    try {
      const r = await fetch('{{ route('admin.live.state', $session) }}', { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
      const s = await r.json();
      for (const q of s.questions) {
        const card = document.querySelector(`[data-id="${q.id}"]`);
        if (!card) continue;
        card.querySelector('.js-count').textContent = q.answers;
        const st = card.querySelector('.js-status');
        st.textContent = labels[q.status]; st.className = 'tag js-status ' + q.status;
        card.querySelector('.js-results').classList.toggle('hidden', !q.show_results);
        card.classList.toggle('current', s.current_question_id === q.id);
      }
    } catch (e) {}
    setTimeout(poll, 3000);
  }
  setTimeout(poll, 3000);
})();
</script>
@endpush
