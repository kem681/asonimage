@extends('layouts.workshop')

@section('title', $group->name)

@section('content')
    <p class="eyebrow"><a href="{{ route('workshop.group.index') }}">Groupe</a> / {{ $group->name }}</p>
    <h1 class="h1">{{ $group->name }} @if($group->isSilent($now))<span class="tag soft">silencieux</span>@endif</h1>

    <div class="card">
        <p class="eyebrow">Le code à donner</p>
        <div class="code-big">{{ $group->code }}</div>
        <p class="muted small">Un compagnon le saisit dans « Rejoindre un groupe ». {{ $members->count() }} / {{ \App\Models\WorkshopGroup::MAX_MEMBERS }} membres.</p>
    </div>

    <div class="card">
        <p class="eyebrow">Membres</p>
        <div class="chips">
            @foreach($members as $member)
                <span class="chip">{{ $member->firstName() }}</span>
            @endforeach
        </div>
    </div>

    <div class="card {{ $group->isSilent($now) ? '' : 'accent' }}">
        <p class="eyebrow">Dernier échange déclaré</p>
        @if($group->last_contact_at)
            <p class="card-title">Il y a {{ $group->daysSinceLastContact($now) }} jour{{ $group->daysSinceLastContact($now) > 1 ? 's' : '' }}</p>
            <p class="muted small">Le {{ $group->last_contact_at->timezone('Europe/Zurich')->translatedFormat('l j F') }}.</p>
        @else
            <p class="card-title">Aucun pour l'instant</p>
        @endif
        <form method="POST" action="{{ route('workshop.group.contact', $group) }}" class="btn-row">
            @csrf
            <button type="submit" class="btn gold">On s'est parlé</button>
        </form>
        <p class="muted small" style="margin-top:0.6rem">En vrai, par téléphone ou par message : peu importe, tant que c'est réel. Passé {{ \App\Models\WorkshopGroup::SILENT_AFTER_DAYS }} jours, le groupe est marqué silencieux.</p>
    </div>

    <div class="card">
        <p class="eyebrow">Prochain rendez-vous</p>
        @if($group->next_meeting_at)
            <p class="card-title">{{ $group->next_meeting_at->timezone('Europe/Zurich')->translatedFormat('l j F, H\hi') }}</p>
        @else
            <p class="muted small">Rien de fixé.</p>
        @endif
        <form method="POST" action="{{ route('workshop.group.meeting', $group) }}" class="stack" style="margin-top:0.8rem">
            @csrf
            <div>
                <label for="next_meeting_at">Fixer ou modifier</label>
                <input type="datetime-local" id="next_meeting_at" name="next_meeting_at" value="{{ $group->next_meeting_at?->timezone('Europe/Zurich')->format('Y-m-d\TH:i') }}">
            </div>
            <button type="submit" class="btn secondary sm">Enregistrer</button>
        </form>
    </div>

    <form method="POST" action="{{ route('workshop.group.leave', $group) }}" onsubmit="return confirm('Quitter ce groupe ?');" style="margin-top:1.5rem">
        @csrf
        <button type="submit" class="link-btn muted small">Quitter le groupe</button>
    </form>
@endsection
