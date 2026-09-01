@extends('layouts.app')

@section('title', 'Publier une ressource')

@section('content')
    <p class="breadcrumb"><a href="{{ route('membres.index') }}">Ressources</a> / <a href="{{ route('admin.resources.index') }}">Admin</a> / Nouvelle ressource</p>
    <h1 class="page-title">Publier une <em>ressource</em></h1>

    <form class="stack" method="POST" action="{{ route('admin.resources.store') }}" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="title">Titre</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>
        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>
        </div>
        <div>
            <label for="edition_id">Édition</label>
            <select id="edition_id" name="edition_id" required>
                @foreach($editions as $edition)
                    <option value="{{ $edition->id }}">{{ $edition->label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="day">Jour</label>
            <select id="day" name="day" required>
                @for($d = 1; $d <= 5; $d++)
                    <option value="{{ $d }}">Jour {{ $d }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label for="file">Fichier (PDF ou audio)</label>
            <p class="card-meta" style="margin-bottom:0.4rem;">Les PowerPoints doivent être exportés en PDF avant l'envoi (ils ne sont pas consultables en ligne autrement). Audio : mp3, wav, ogg ou m4a.</p>
            <input type="file" id="file" name="file" accept=".pdf,.mp3,.wav,.ogg,.m4a" required>
        </div>
        <button type="submit" class="btn">Publier</button>
    </form>
@endsection
