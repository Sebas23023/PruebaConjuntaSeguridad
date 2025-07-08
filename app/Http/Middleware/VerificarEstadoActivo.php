<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarEstadoActivo
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->estado !== 'activo') {
            Auth::logout();
            $motivo = $user->motivo_inactivo ?? 'Sin motivo especificado';
            return redirect()->route('login')->withErrors([
                'estado' => 'Tu cuenta fue desactivada. Motivo: ' . $motivo
            ]);
        }
        return $next($request);
    }
} 