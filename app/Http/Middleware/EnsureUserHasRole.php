<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Dozvoljava pristup samo korisnicima sa jednom od navedenih uloga.
     * Upotreba u ruti: ->middleware('role:admin,designer')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Nemate ovlaštenje za pristup ovoj stranici.');
        }

        return $next($request);
    }
}
