<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sur le sous-domaine dedie a l'outil (app.asonimage.ch), la racine du site
 * envoie directement sur /3x30 : la landing page du seminaire n'a rien a y
 * faire, et l'adresse a retenir reste courte.
 */
class RedirectWorkshopHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $hosts = array_filter(array_map('trim', explode(',', (string) config('app.workshop_hosts', ''))));

        if ($request->path() === '/' && in_array(strtolower($request->getHost()), $hosts, true)) {
            return redirect('/3x30');
        }

        return $next($request);
    }
}
