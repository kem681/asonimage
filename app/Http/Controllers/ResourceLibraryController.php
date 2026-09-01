<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Resource;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ResourceLibraryController extends Controller
{
    public function index(): View
    {
        $editions = Edition::withCount('resources')->orderByDesc('year')->get();

        return view('membres.editions', ['editions' => $editions]);
    }

    public function showEdition(Edition $edition): View
    {
        $counts = $edition->resources()
            ->selectRaw('day, count(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        return view('membres.jours', [
            'edition' => $edition,
            'counts' => $counts,
        ]);
    }

    public function showDay(Edition $edition, int $day): View
    {
        abort_unless($day >= 1 && $day <= 5, 404);

        $resources = $edition->resources()->where('day', $day)->orderBy('title')->get();

        return view('membres.ressources', [
            'edition' => $edition,
            'day' => $day,
            'resources' => $resources,
        ]);
    }

    public function show(Resource $resource): View
    {
        return view('membres.ressource', ['resource' => $resource]);
    }

    public function stream(Resource $resource): BinaryFileResponse
    {
        abort_unless(Storage::disk('public')->exists($resource->file_path), 404);

        // Servi en "inline" (jamais "attachment") : le fichier s'ouvre dans le
        // navigateur pour consultation, il ne se telecharge pas directement.
        // BinaryFileResponse (plutot qu'un StreamedResponse) gere nativement
        // les requetes "Range", necessaires pour le seek audio et un
        // chargement progressif cote pdf.js sur les gros fichiers.
        $response = new BinaryFileResponse(Storage::disk('public')->path($resource->file_path));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $resource->original_filename);

        return $response;
    }
}
