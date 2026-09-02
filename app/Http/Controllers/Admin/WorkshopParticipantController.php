<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkshopParticipant;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Liste de contact des participants 3x30 (nom, email, code, date d'entree).
 * Aucun contenu ecrit par les participants ne transite ici.
 */
class WorkshopParticipantController extends Controller
{
    public function index(): View
    {
        return view('admin.workshop.participants', [
            'participants' => WorkshopParticipant::with(['user', 'workshopCode'])->latest('joined_at')->paginate(100),
        ]);
    }

    public function export(): StreamedResponse
    {
        $filename = 'participants-3x30-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
            fputcsv($out, ['Nom', 'Email', 'Code atelier', 'Atelier', 'Entré le'], ';');

            WorkshopParticipant::with(['user', 'workshopCode'])
                ->orderBy('joined_at')
                ->chunk(200, function ($participants) use ($out) {
                    foreach ($participants as $participant) {
                        fputcsv($out, [
                            $participant->user?->name ?? '',
                            $participant->email,
                            $participant->workshopCode?->code ?? '',
                            $participant->workshopCode?->label ?? '',
                            $participant->joined_at?->format('d.m.Y') ?? '',
                        ], ';');
                    }
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
