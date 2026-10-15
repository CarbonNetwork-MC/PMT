<?php

namespace App\Http\Middleware;

use App\Models\Sprint;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSprintIsStarted
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
        if ($sprint->status === 'planned') {
            return redirect()->route('projects.sprints.render', ['uuid' => $sprint->project->uuid])->error(__('board.messages.sprint_not_started'));
        }

        return $next($request);
    }
}
