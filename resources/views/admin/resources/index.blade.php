@extends('layouts.app')

@section('title', 'Admin — ressources')

@section('content')
    <p class="breadcrumb"><a href="{{ route('membres.index') }}">Ressources</a> / Admin / Ressources</p>
    <h1 class="page-title">Gérer les <em>ressources</em></h1>

    <p style="margin-bottom:2rem;"><a href="{{ route('admin.resources.create') }}" class="btn" style="display:inline-block; text-decoration:none; padding: 0.85rem 1.8rem; background: var(--forest); color: var(--cream); font-size: 0.85rem; letter-spacing: 0.08em; text-transform: uppercase;">+ Publier une ressource</a></p>

    <table class="data">
        <thead>
            <tr><th>Titre</th><th>Édition</th><th>Jour</th><th>Type</th><th>Fichier</th><th>Publié le</th><th></th></tr>
        </thead>
        <tbody>
            @foreach($resources as $resource)
                <tr>
                    <td>{{ $resource->title }}</td>
                    <td>{{ $resource->edition->label ?? '—' }}</td>
                    <td>J{{ $resource->day }}</td>
                    <td>{{ $resource->isAudio() ? 'Audio' : 'Document' }}</td>
                    <td>{{ $resource->original_filename }}</td>
                    <td>{{ $resource->created_at->format('d/m/Y') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.resources.destroy', $resource) }}" onsubmit="return confirm('Supprimer cette ressource ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="link" style="background:none;border:none;color:#b3413a;cursor:pointer;font-size:0.8rem;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:1.5rem;">{{ $resources->links() }}</div>
@endsection
