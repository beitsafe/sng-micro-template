<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AuthenticateAccess
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
        if (in_array(env('INTERNAL_SECRET'), [$request->header('Authorization'), 'ALLOW_ME'])) {
            return $next($request);
        }

        abort(Response::HTTP_UNAUTHORIZED);
    }
}
