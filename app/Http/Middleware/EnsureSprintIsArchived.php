<?php

namespace App\Http\Middleware;

use App\Models\Sprint;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSprintIsArchived
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) return redirect()->route('login');

        $sprintUuid = $request->route('sprintUuid');
        if (!$sprintUuid) abort(400, 'Sprint UUID is required.');

        $sprint = Sprint::where('uuid', $sprintUuid)->firstOrFail();
        if ($sprint->is_archived === false) {
            return redirect()->route('projects.archive.render', ['uuid' => $sprint->project->uuid])->error(__('archive.messages.sprint_not_archived', ['name' => $sprint->name]));
        }

        return $next($request);
    }
}
