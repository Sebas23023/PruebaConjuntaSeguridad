<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si hay un usuario autenticado
        if ($user) {
            // Verificar si el usuario existe en la base de datos y está activo
            $dbUser = User::withTrashed()->find($user->id);
            
            if (!$dbUser || $dbUser->status !== 'active') {
                $reason = $dbUser->deactivation_reason ?? 'Tu cuenta ha sido desactivada por un administrador.';
                
                // Registrar en la auditoría antes de cerrar sesión
                activity()
                    ->causedBy($dbUser->deleted_by ?? null)
                    ->performedOn($dbUser)
                    ->withProperties([
                        'reason' => $reason,
                        'status' => $dbUser->status ?? 'deleted',
                    ])
                    ->log('Usuario desactivado/eliminado');
                
                Auth::logout();
                
                return redirect()->route('login')
                    ->with('error', $reason);
            }
        }

        return $next($request);
    }
}