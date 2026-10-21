<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminAuth Middleware
 *
 * Verifica que el usuario autenticado tenga acceso al panel de administración.
 * Actualmente el rol de administrador se gestiona mediante el sistema de
 * autenticación de Laravel (Breeze). Este middleware puede extenderse en el
 * futuro para añadir comprobaciones de rol más granulares.
 */
class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Comprueba que el usuario esté autenticado
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder al panel de administración.');
        }

        return $next($request);
    }
}
