@extends('layouts.app')

@section('title', $resource->title)

@section('content')
    <p class="breadcrumb">
        <a href="{{ route('membres.index') }}">Ressources</a> /
        <a href="{{ route('membres.edition', $resource->edition) }}">{{ $resource->edition->label }}</a> /
        <a href="{{ route('membres.jour', [$resource->edition, $resource->day]) }}">Jour {{ $resource->day }}</a>
    </p>
    <h1 class="page-title">{{ $resource->title }}</h1>
    @if($resource->description)
        <p class="page-sub">{{ $resource->description }}</p>
    @endif

    @if($resource->isAudio())
        <audio controls controlsList="nodownload noplaybackrate" style="width:100%;">
            <source src="{{ route('membres.ressources.fichier', $resource) }}">
        </audio>
    @else
        <div class="pdf-viewer">
            <iframe src="{{ route('membres.ressources.fichier', $resource) }}#toolbar=0&navpanes=0" title="{{ $resource->title }}"></iframe>
        </div>
        <p class="page-sub" style="margin-top:1rem; font-size:0.8rem;">Document en consultation uniquement.</p>
    @endif
@endsection
