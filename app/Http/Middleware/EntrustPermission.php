<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

class EntrustPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null $permissions
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        if (!auth()->check() || !auth()->user()->can(explode('|', $permissions))) {
            abort(403);
        }

        return $next($request);
    }
}
