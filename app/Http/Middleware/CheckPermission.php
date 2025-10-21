<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {

        if(!auth()->check()){
            return redirect()->route('login');
        }

        // Si el usuario tiene rol Super Admin, permitir todo
        if (auth()->user()->hasRole('Super Admin')) {
            return $next($request);
        }


        // Verificar si el usuario tiene alguno de los permisos requeridos
        foreach ($permissions as $permission) {
            if (auth()->user()->can($permission)) {
                return $next($request);
            }
        }

        // Si no tiene ningún permiso, denegar acceso
        abort(403, 'No tienes permisos para acceder a esta página.');
    }
}
