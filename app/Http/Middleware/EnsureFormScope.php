<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureFormScope
{
    public function handle(Request $request, Closure $next, string $formSlug)
    {
        abort_unless($request->user()->canAccess($formSlug), 403);

        return $next($request);
    }
}