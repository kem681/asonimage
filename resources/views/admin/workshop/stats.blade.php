@extends('layouts.app')

@section('title', 'Admin — chiffres 3x30')

@section('content')
    <p class="breadcrumb"><a href="{{ route('membres.index') }}">Ressources</a> / Admin / 3x30 / Chiffres</p>
    <h1 class="page-title">Chiffres <em>3x30</em></h1>
    <p class="page-sub">Agrégés et anonymes, pour ajuster l'atelier. Aucun contenu individuel n'est accessible.</p>

    <div class="card-grid">
        <div class="card"><div class="card-eyebrow">Participants</div><div class="card-title">{{ $participants }}</div></div>
        <div class="card"><div class="card-eyebrow">Ont fait le diagnostic</div><div class="card-title">{{ $usersWithDiagnostic }}</div><div class="card-meta">{{ $diagnostics }} diagnostic{{ $diagnostics > 1 ? 's' : '' }} au total</div></div>
        <div class="card"><div class="card-eyebrow">Gestes en cours</div><div class="card-title">{{ $activeAnchors }}</div></div>
        <div class="card"><div class="card-eyebrow">Revues faites</div><div class="card-title">{{ $reviews }}</div></div>
        <div class="card"><div class="card-eyebrow">Groupes</div><div class="card-title">{{ $groups }}</div><div class="card-meta">{{ $groupMembers }} appartenance{{ $groupMembers > 1 ? 's' : '' }}</div></div>
    </div>

    <h2 class="card-title" style="margin-top:3rem; margin-bottom:1rem;">Axe phare (dernier diagnostic de chacun)</h2>
    <table class="data" style="max-width:480px;">
        <thead><tr><th>Axe</th><th>Participants</th></tr></thead>
        <tbody>
            @foreach($content->axes() as $key => $axis)
                <tr><td>{{ $axis['label'] }}</td><td>{{ $axisCounts[$key] ?? 0 }}</td></tr>
            @endforeach
        </tbody>
    </table>
@endsection
