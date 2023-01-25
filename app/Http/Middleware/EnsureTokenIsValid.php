<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (ltrim($request->header('Authorization'),'Bearer ') == env('APP_SECRET', Str::random())) {
            return $next($request);
        }

        abort(Response::HTTP_UNAUTHORIZED);
    }
}
