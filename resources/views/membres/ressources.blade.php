@extends('layouts.app')

@section('title', $edition->label.' — Jour '.$day)

@section('content')
    <p class="breadcrumb"><a href="{{ route('membres.index') }}">Ressources</a> / <a href="{{ route('membres.edition', $edition) }}">{{ $edition->label }}</a> / Jour {{ $day }}</p>
    <h1 class="page-title">{{ $edition->label }} — <em>jour {{ $day }}</em></h1>

    @if($resources->isEmpty())
        <div class="empty-state">Aucune ressource publiée pour ce jour pour le moment.</div>
    @else
        @foreach($resources as $resource)
            <div class="resource-row">
                <div>
                    <div class="title">{{ $resource->title }}</div>
                    @if($resource->description)
                        <div class="desc">{{ $resource->description }}</div>
                    @endif
                </div>
                <a class="download" href="{{ route('membres.ressources.telecharger', $resource) }}">Télécharger</a>
            </div>
        @endforeach
    @endif
@endsection
