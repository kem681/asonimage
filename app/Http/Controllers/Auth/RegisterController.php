<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthorizedEmail;
use App\Models\User;
use App\Models\WorkshopCode;
use App\Models\WorkshopParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register', ['workshop' => false, 'code' => '']);
    }

    /** Formulaire de creation de compte par code d'atelier 3x30 (lien donne en fin d'atelier). */
    public function showWorkshop(Request $request): View
    {
        return view('auth.register', [
            'workshop' => true,
            'code' => (string) $request->query('code', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'workshop_code' => ['nullable', 'string', 'max:32'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $email = strtolower($data['email']);

        $authorized = AuthorizedEmail::where('email', $email)->first();
        $workshopCode = null;

        if (! empty($data['workshop_code'])) {
            $workshopCode = WorkshopCode::findActive($data['workshop_code']);

            if (! $workshopCode) {
                return back()->withInput()->withErrors([
                    'workshop_code' => "Ce code d'atelier n'est pas reconnu (ou n'est plus actif). Vérifie-le avec la personne qui a animé l'atelier.",
                ]);
            }
        } elseif (! $authorized) {
            return back()->withInput()->withErrors([
                'email' => "Cet email n'est pas reconnu comme inscrit au séminaire. Si tu viens d'un atelier 3x30, entre le code reçu en fin d'atelier. Sinon, écris-nous à contact@asonimage.ch.",
            ]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
            'edition_id' => $authorized?->edition_id,
        ]);

        if ($workshopCode) {
            WorkshopParticipant::create([
                'user_id' => $user->id,
                'workshop_code_id' => $workshopCode->id,
                'email' => $email,
                'joined_at' => now(),
            ]);
        }

        Auth::login($user);

        return redirect()->route($workshopCode ? 'workshop.index' : 'membres.index');
    }
}
