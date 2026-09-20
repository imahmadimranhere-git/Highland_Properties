<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route-level role gate: ->middleware('role:super_admin')
 *
 * This blocks the whole area. It does not replace query scoping —
 * every consultant query must still go through Lead::visibleTo() and
 * Report::visibleTo(), because a role check alone cannot stop one
 * consultant from opening another consultant's record by id.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role?->slug, $roles, true)) {
            abort(403, 'You do not have access to this area.');
        }

        return $next($request);
    }
}
