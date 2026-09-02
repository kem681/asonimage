@extends('layouts.app')

@section('title', 'Admin — participants 3x30')

@section('content')
    <p class="breadcrumb"><a href="{{ route('membres.index') }}">Ressources</a> / Admin / 3x30 / Participants</p>
    <h1 class="page-title">Participants <em>3x30</em></h1>
    <p class="page-sub">Liste de contact (nom, email, atelier). Rien de ce qu'ils écrivent dans l'outil n'apparaît ici.</p>

    <p style="margin-bottom:2rem;"><a href="{{ route('admin.workshop.participants.export') }}" class="btn" style="display:inline-block; text-decoration:none; padding: 0.85rem 1.8rem; background: var(--forest); color: var(--cream); font-size: 0.85rem; letter-spacing: 0.08em; text-transform: uppercase;">Exporter en CSV</a></p>

    <table class="data">
        <thead>
            <tr><th>Nom</th><th>Email</th><th>Atelier</th><th>Entré le</th></tr>
        </thead>
        <tbody>
            @forelse($participants as $participant)
                <tr>
                    <td>{{ $participant->user->name ?? '—' }}</td>
                    <td>{{ $participant->email }}</td>
                    <td>{{ $participant->workshopCode->label ?? '—' }}</td>
                    <td>{{ $participant->joined_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Aucun participant pour l'instant.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1.5rem;">{{ $participants->links() }}</div>
@endsection
