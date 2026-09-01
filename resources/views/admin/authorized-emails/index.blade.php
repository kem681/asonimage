@extends('layouts.app')

@section('title', 'Admin — emails autorisés')

@section('content')
    <p class="breadcrumb"><a href="{{ route('membres.index') }}">Ressources</a> / Admin / Emails autorisés</p>
    <h1 class="page-title">Emails <em>autorisés</em></h1>
    <p class="page-sub">Un email doit figurer dans cette liste pour qu'un compte membre puisse être créé.</p>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 3rem;">
        <div>
            <h2 class="card-title" style="margin-bottom:1rem;">Ajouter un email</h2>
            <form class="stack" method="POST" action="{{ route('admin.emails.store') }}">
                @csrf
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name">
                </div>
                <div>
                    <label for="edition_id">Édition</label>
                    <select id="edition_id" name="edition_id" required>
                        @foreach($editions as $edition)
                            <option value="{{ $edition->id }}">{{ $edition->label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn">Ajouter</button>
            </form>
        </div>

        <div>
            <h2 class="card-title" style="margin-bottom:1rem;">Importer un CSV</h2>
            <p class="card-meta" style="margin-bottom:1rem;">Seules les colonnes "Participant" et "Mail" sont utilisées.</p>
            <form class="stack" method="POST" action="{{ route('admin.emails.import') }}" enctype="multipart/form-data">
                @csrf
                <div>
                    <label for="edition_id_csv">Édition</label>
                    <select id="edition_id_csv" name="edition_id" required>
                        @foreach($editions as $edition)
                            <option value="{{ $edition->id }}">{{ $edition->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="csv">Fichier CSV</label>
                    <input type="file" id="csv" name="csv" accept=".csv,text/csv" required>
                </div>
                <button type="submit" class="btn">Importer</button>
            </form>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr><th>Email</th><th>Nom</th><th>Édition</th><th>Source</th><th>Ajouté le</th></tr>
        </thead>
        <tbody>
            @foreach($authorizedEmails as $entry)
                <tr>
                    <td>{{ $entry->email }}</td>
                    <td>{{ $entry->name ?? '—' }}</td>
                    <td>{{ $entry->edition->label ?? '—' }}</td>
                    <td>{{ $entry->source }}</td>
                    <td>{{ $entry->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:1.5rem;">{{ $authorizedEmails->links() }}</div>
@endsection
