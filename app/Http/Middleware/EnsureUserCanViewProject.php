<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanViewProject
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        $user = $request->user();

        if ($user->can('manage-projects')) {
            return $next($request);
        }

        $project = Project::where('uuid', $request->route('uuid'))
            ->firstOrFail();

        $isMember = $project->members()
            ->where('user_uuid', $user->uuid)
            ->exists();

        $isOwner = $project->owner_uuid === $user->uuid;

        abort_unless(
            $isMember || $isOwner,
            403,
            'You do not have permission to access this resource.'
        );

        return $next($request);
    }
}
