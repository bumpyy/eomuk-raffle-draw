<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the specific session key exists
        if (! session()->get('is_admin_logged_in')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
