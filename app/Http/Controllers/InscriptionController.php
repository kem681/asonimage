<?php

namespace App\Http\Controllers;

use App\Models\AuthorizedEmail;
use App\Models\Edition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class InscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'dob' => ['required', 'string', 'max:255'],
            'tel' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
            'eglise_yn' => ['nullable', 'string', 'max:255'],
            'eglise' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'source_autre' => ['nullable', 'string', 'max:255'],
            'logement' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'accessibilite' => ['nullable', 'string', 'max:1000'],
            'question_camp' => ['nullable', 'string', 'max:2000'],
            'autres_questions' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $data = $validator->validated();

        $edition = Edition::orderByDesc('year')->firstOrFail();

        AuthorizedEmail::updateOrCreate(
            ['email' => strtolower($data['email'])],
            [
                'name' => trim($data['prenom'].' '.$data['nom']),
                'edition_id' => $edition->id,
                'source' => AuthorizedEmail::SOURCE_INSCRIPTION,
            ]
        );

        $this->sendAdminNotification($data);
        $this->sendParticipantConfirmation($data);

        return response()->json(['success' => true]);
    }

    private function sendAdminNotification(array $data): void
    {
        $eglise = $data['eglise_yn'] ?? '';
        if (($data['eglise_yn'] ?? '') === 'oui' && ! empty($data['eglise'])) {
            $eglise = $data['eglise'];
        }

        $source = ($data['source'] ?? '') === 'autre' ? ($data['source_autre'] ?? '') : ($data['source'] ?? '');

        $body = "Nouvelle inscription - A Son Image\n"
            .str_repeat('=', 40)."\n\n"
            ."Prenom        : {$data['prenom']}\n"
            ."Nom           : {$data['nom']}\n"
            ."Email         : {$data['email']}\n"
            ."Telephone     : ".($data['tel'] ?? '')."\n"
            ."Naissance     : {$data['dob']}\n"
            ."Ville/Region  : ".($data['ville'] ?? '')."\n"
            ."Eglise        : {$eglise}\n"
            ."Source        : {$source}\n"
            ."Logement      : ".($data['logement'] ?? '')."\n"
            ."Allergies     : ".($data['allergies'] ?? '')."\n"
            ."Accessibilite : ".($data['accessibilite'] ?? '')."\n"
            ."Question camp : ".($data['question_camp'] ?? '')."\n"
            ."Autres        : ".($data['autres_questions'] ?? '')."\n"
            ."\nDate : ".now()->format('d/m/Y H:i')."\n";

        Mail::raw($body, function ($message) use ($data) {
            $message->to(config('mail.from.address'))
                ->subject('Nouvelle inscription - '.$data['prenom'].' '.$data['nom']);
        });
    }

    private function sendParticipantConfirmation(array $data): void
    {
        $body = "Bonjour {$data['prenom']},\n\n"
            ."Merci beaucoup pour ton inscription au séminaire À Son Image !\n\n"
            ."On se réjouit de t'accueillir du dimanche 2 au vendredi 7 août 2026 à la Salle de Saint Romain le désert, sur le plateau ardéchois.\n\n"
            ."Ton inscription a bien été enregistrée. Tu recevras d'ici quelques jours un email avec toutes les informations pratiques (accès, logement, programme détaillé).\n\n"
            ."En attendant, si tu as la moindre question, n'hésite pas à nous écrire à contact@asonimage.ch - on est là !\n\n"
            ."On a hâte de vivre cette semaine avec toi.\n\n"
            ."Chaleureusement,\nL'équipe À Son Image\nCécile, Val, Jonathan, Flo et Marc\n";

        Mail::raw($body, function ($message) use ($data) {
            $message->to($data['email'], $data['prenom'].' '.$data['nom'])
                ->subject('Confirmation inscription - À Son Image');
        });
    }
}
