@extends('layouts.workshop')

@section('title', 'Groupe')

@section('content')
    <p class="eyebrow">Groupe</p>
    <h1 class="h1">Des gars qui savent <em>où j'en suis</em></h1>
    <p class="lead">{{ $content->text('group_intro') }}</p>

    @foreach($groups as $group)
        <a href="{{ route('workshop.group.show', $group) }}" class="card card-link {{ $group->isSilent() ? '' : 'accent' }}">
            <p class="card-title">{{ $group->name }} @if($group->isSilent())<span class="tag soft">silencieux</span>@endif</p>
            <p class="muted small">{{ $group->members_count }} membre{{ $group->members_count > 1 ? 's' : '' }} · dernier échange déclaré il y a {{ $group->daysSinceLastContact() }} jour{{ $group->daysSinceLastContact() > 1 ? 's' : '' }}</p>
        </a>
    @endforeach

    <div class="card">
        <p class="eyebrow">Rejoindre un groupe</p>
        <form method="POST" action="{{ route('workshop.group.join') }}" class="stack">
            @csrf
            <div>
                <label for="code">Le code reçu d'un compagnon</label>
                <input type="text" id="code" name="code" required maxlength="12" value="{{ old('code') }}" autocapitalize="characters" autocomplete="off" style="letter-spacing:0.2em;text-transform:uppercase">
            </div>
            <button type="submit" class="btn secondary">Rejoindre</button>
        </form>
    </div>

    <div class="card">
        <p class="eyebrow">Créer un groupe</p>
        <form method="POST" action="{{ route('workshop.group.store') }}" class="stack">
            @csrf
            <div>
                <label for="name">Nom du groupe</label>
                <input type="text" id="name" name="name" required maxlength="80" value="{{ old('name') }}" placeholder="Les gars du jeudi">
                <p class="hint">Tu recevras un code à donner à tes compagnons. Jusqu'à {{ \App\Models\WorkshopGroup::MAX_MEMBERS }} par groupe.</p>
            </div>
            <button type="submit" class="btn">Créer</button>
        </form>
    </div>
@endsection
