<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auditoria;

class AuditoriaController extends Controller
{
    public function index()
    {
        $auditorias = Auditoria::with('usuario')->orderByDesc('created_at')->get();
        return view('auditorias.index', compact('auditorias'));
    }
} 