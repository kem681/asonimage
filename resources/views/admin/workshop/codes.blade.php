@extends('layouts.app')

@section('title', 'Admin — codes 3x30')

@section('content')
    <p class="breadcrumb"><a href="{{ route('membres.index') }}">Ressources</a> / Admin / 3x30 / Codes d'atelier</p>
    <h1 class="page-title">Codes <em>d'atelier</em> 3x30</h1>
    <p class="page-sub">Un code par atelier (ou par session). Il se donne en fin d'atelier ; un homme l'utilise pour créer son compte ou pour entrer dans 3x30 s'il a déjà un compte.</p>

    <div style="margin-bottom:3rem; max-width:460px;">
        <h2 class="card-title" style="margin-bottom:1rem;">Créer un code</h2>
        <form class="stack" method="POST" action="{{ route('admin.workshop.codes.store') }}">
            @csrf
            <div>
                <label for="code">Code (lettres et chiffres)</label>
                <input type="text" id="code" name="code" required maxlength="32" value="{{ old('code') }}" placeholder="3X30-NOV26">
            </div>
            <div>
                <label for="label">Libellé</label>
                <input type="text" id="label" name="label" required maxlength="120" value="{{ old('label') }}" placeholder="Atelier 3x30, Lausanne, novembre 2026">
            </div>
            <button type="submit" class="btn">Créer</button>
        </form>
    </div>

    <table class="data">
        <thead>
            <tr><th>Code</th><th>Libellé</th><th>Participants</th><th>Créé le</th><th>État</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($codes as $code)
                <tr>
                    <td><strong>{{ $code->code }}</strong></td>
                    <td>{{ $code->label }}</td>
                    <td>{{ $code->participants_count }}</td>
                    <td>{{ $code->created_at->format('d/m/Y') }}</td>
                    <td>{{ $code->is_active ? 'Actif' : 'Désactivé' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.workshop.codes.toggle', $code) }}">
                            @csrf
                            <button type="submit" class="link" style="background:none;border:none;color:var(--forest);cursor:pointer;font-size:0.8rem;">{{ $code->is_active ? 'Désactiver' : 'Réactiver' }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Aucun code pour l'instant.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p style="margin-top:2rem; font-size:0.85rem;">Lien à donner avec le code : <code>{{ route('workshop.register') }}?code=LECODE</code></p>
@endsection
