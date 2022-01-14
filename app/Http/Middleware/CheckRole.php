<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {

        if (!in_array(auth()->user()->roleId, $roles)) {
            return response('não autorizado', 403);
        }

        // if ($role == 'admin' && auth()->user()->roleId != 1) {
        //     return response('não autorizado', 403);
        // }

        // if ($role == 'condomino' && auth()->user()->roleId != 2) {
        //     return response('não autorizado', 403);
        // }

        // if ($role == 'morador' && auth()->user()->roleId != 3) {
        //     return response('não autorizado', 403);
        // }



        return $next($request);
    }
}
