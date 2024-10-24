<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $permission
     * 
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = auth()->user();

        // Ensure the user has a role and permissions are stored in the role
        if ($user && $user->role) {
            // Decode the permissions from the JSON string in the role
            $rolePermissions = json_decode($user->role->permissions, true);
            
            // Check if the permission exists and is set to true
            if (isset($rolePermissions[$permission]) && $rolePermissions[$permission] === true) {
                return $next($request);
            }
        }

        // If no permission, redirect to the dashboard
        return redirect()->route('dashboard.render');
    }
}
