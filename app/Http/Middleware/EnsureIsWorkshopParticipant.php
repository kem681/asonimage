<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsWorkshopParticipant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if (! $user->canAccessWorkshop()) {
            return redirect()
                ->route('workshop.code')
                ->with('status', "L'outil 3x30 est réservé aux participants d'un atelier. Entre le code reçu en fin d'atelier.");
        }

        return $next($request);
    }
}
