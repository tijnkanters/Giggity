<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToOrganization
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Auto-set current_organization_id if not set
        if (!$user->current_organization_id) {
            $firstOrg = $user->organizations()->first();

            if (!$firstOrg) {
                abort(403, 'You do not belong to any organization.');
            }

            $user->update(['current_organization_id' => $firstOrg->id]);
            $user->refresh();
        }

        // Verify the user actually belongs to the selected organization
        $belongsToOrg = $user->organizations()
            ->where('organizations.id', $user->current_organization_id)
            ->exists();

        if (!$belongsToOrg) {
            abort(403, 'You do not have access to this organization.');
        }

        return $next($request);
    }
}
