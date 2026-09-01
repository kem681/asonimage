<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Resource;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function download(Resource $resource): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($resource->file_path), 404);

        return Storage::disk('public')->download($resource->file_path, $resource->original_filename);
    }
}
