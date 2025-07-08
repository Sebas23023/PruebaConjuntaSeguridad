<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Asignatura;

class AsignaturaController extends Controller
{
    public function index(Request $request)
    {
        // Obtener las asignaturas asignadas al docente autenticado
        $asignaturas = Auth::user()->asignaturas()->get();
        return view('asignaturas.index', compact('asignaturas'));
    }
} 