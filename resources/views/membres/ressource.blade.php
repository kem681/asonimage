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
        <div class="pdf-viewer" id="pdf-viewer" oncontextmenu="return false;">
            <p class="pdf-loading">Chargement du document…</p>
        </div>
        <p class="page-sub" style="margin-top:1rem; font-size:0.8rem;">Document en consultation uniquement.</p>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"
                integrity="sha512-q+4liFwdPC/bNdhUpZx6aXDx/h77yEQtn4I1slHydcbZK34nLaR3cAeYSJshoxIOq3mjEf7xJE8YWIUHMn+oCQ=="
                crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
        (function () {
            const container = document.getElementById('pdf-viewer');

            function setMessage(text) {
                container.textContent = '';
                const p = document.createElement('p');
                p.className = 'pdf-loading';
                p.textContent = text;
                container.appendChild(p);
            }

            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const url = @json(route('membres.ressources.fichier', $resource));

            pdfjsLib.getDocument({ url: url, withCredentials: true }).promise.then(function (pdf) {
                container.textContent = '';
                const renderPage = function (num) {
                    return pdf.getPage(num).then(function (page) {
                        const scale = (container.clientWidth || 800) / page.getViewport({ scale: 1 }).width;
                        const viewport = page.getViewport({ scale: scale * (window.devicePixelRatio || 1) });
                        const canvas = document.createElement('canvas');
                        canvas.className = 'pdf-page';
                        canvas.oncontextmenu = function () { return false; };
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;
                        canvas.style.width = '100%';
                        container.appendChild(canvas);
                        return page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
                    });
                };
                let chain = Promise.resolve();
                for (let i = 1; i <= pdf.numPages; i++) {
                    chain = chain.then(() => renderPage(i));
                }
                return chain;
            }).catch(function (err) {
                setMessage('Impossible de charger le document. Réessaie ou contacte-nous à contact@asonimage.ch.');
                console.error(err);
            });
        })();
        </script>
    @endif
@endsection
