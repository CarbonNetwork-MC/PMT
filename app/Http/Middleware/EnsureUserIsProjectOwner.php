<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsProjectOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user->can('manage-projects')) return $next($request);

        $project = Project::where('uuid', $request->route('uuid'))
            ->firstOrFail();

        if ($user->uuid !== $project->owner_uuid) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
