<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (!auth()->user()->active) {
                return to_route('suspended');
            }
            
            if (auth()->user()->status != "user") {
                return to_route('admin.dashboard');
            }

            return $next($request);
        }

        return to_route('login');
    }
}
