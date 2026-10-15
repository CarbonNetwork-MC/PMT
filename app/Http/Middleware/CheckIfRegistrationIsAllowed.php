<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfRegistrationIsAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.allow_registration')) {
            return redirect()->route('login')->withErrors(['registration' => 'Registrations are currently closed.']);
        }

        return $next($request);
    }
}
