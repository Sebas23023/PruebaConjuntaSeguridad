<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotaController extends Controller
{
    // Registrar una nueva nota
    public function store(Request $request)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:users,id',
            'asignatura_id' => 'required|exists:asignaturas,id',
            'nota_1' => 'required|numeric|min:0|max:20',
            'nota_2' => 'required|numeric|min:0|max:20',
            'nota_3' => 'required|numeric|min:0|max:20',
        ]);

        DB::beginTransaction();
        try {
            $promedio = round((($request->nota_1 + $request->nota_2 + $request->nota_3) / 3), 2);
            $estado_final = $promedio >= 14.5 ? 'aprobado' : 'reprobado';

            $nota = Nota::create([
                'estudiante_id' => $request->estudiante_id,
                'asignatura_id' => $request->asignatura_id,
                'nota_1' => $request->nota_1,
                'nota_2' => $request->nota_2,
                'nota_3' => $request->nota_3,
                'promedio' => $promedio,
                'estado_final' => $estado_final,
            ]);

            DB::commit();
            return response()->json(['message' => 'Nota registrada correctamente', 'nota' => $nota], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al registrar la nota', 'detalle' => $e->getMessage()], 500);
        }
    }

    // Actualizar una nota existente (requiere motivo y auditoría)
    public function update(Request $request, $id)
    {
        $request->validate([
            'nota_1' => 'required|numeric|min:0|max:20',
            'nota_2' => 'required|numeric|min:0|max:20',
            'nota_3' => 'required|numeric|min:0|max:20',
            'motivo' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $nota = Nota::findOrFail($id);
            $promedio = round((($request->nota_1 + $request->nota_2 + $request->nota_3) / 3), 2);
            $estado_final = $promedio >= 14.5 ? 'aprobado' : 'reprobado';

            $nota->update([
                'nota_1' => $request->nota_1,
                'nota_2' => $request->nota_2,
                'nota_3' => $request->nota_3,
                'promedio' => $promedio,
                'estado_final' => $estado_final,
            ]);

            Auditoria::create([
                'usuario_id' => Auth::id(),
                'accion' => 'editar',
                'motivo' => $request->motivo,
                'created_at' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Nota actualizada correctamente', 'nota' => $nota], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar la nota', 'detalle' => $e->getMessage()], 500);
        }
    }

    // Eliminar (soft) una nota
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string',
            'password' => 'required|string',
        ]);
        $nota = Nota::findOrFail($id);
        $admin = Auth::user();
        if (!\Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 403);
        }
        \DB::beginTransaction();
        try {
            $nota->delete();
            Auditoria::create([
                'usuario_id' => $admin->id,
                'accion' => 'eliminar (soft)',
                'motivo' => $request->motivo,
                'created_at' => now(),
            ]);
            \DB::commit();
            return response()->json(['message' => 'Nota eliminada correctamente'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => 'Error al eliminar la nota', 'detalle' => $e->getMessage()], 500);
        }
    }

    // Restaurar una nota eliminada
    public function restore(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string',
            'password' => 'required|string',
        ]);
        $nota = Nota::withTrashed()->findOrFail($id);
        $admin = Auth::user();
        if (!\Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 403);
        }
        \DB::beginTransaction();
        try {
            $nota->restore();
            Auditoria::create([
                'usuario_id' => $admin->id,
                'accion' => 'restaurar',
                'motivo' => $request->motivo,
                'created_at' => now(),
            ]);
            \DB::commit();
            return response()->json(['message' => 'Nota restaurada correctamente'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => 'Error al restaurar la nota', 'detalle' => $e->getMessage()], 500);
        }
    }

    // Borrar definitivamente una nota
    public function forceDelete(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string',
            'password' => 'required|string',
        ]);
        $nota = Nota::withTrashed()->findOrFail($id);
        $admin = Auth::user();
        if (!\Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 403);
        }
        \DB::beginTransaction();
        try {
            $nota->forceDelete();
            Auditoria::create([
                'usuario_id' => $admin->id,
                'accion' => 'borrar definitivo',
                'motivo' => $request->motivo,
                'created_at' => now(),
            ]);
            \DB::commit();
            return response()->json(['message' => 'Nota eliminada permanentemente'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => 'Error al borrar la nota', 'detalle' => $e->getMessage()], 500);
        }
    }

    // Mostrar notas (para docentes y estudiantes)
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('docente')) {
            $asignaturas = $user->asignaturas()->get();
            $asignaturaId = $request->input('asignatura_id');
            $estudiantes = collect();
            $notas = collect();
            if ($asignaturaId) {
                $asignatura = $asignaturas->where('id', $asignaturaId)->first();
                if ($asignatura) {
                    // Obtener estudiantes inscritos en la asignatura
                    $estudiantes = $asignatura->docentes()->wherePivot('asignatura_id', $asignaturaId)->get();
                    // Obtener notas de la asignatura
                    $notas = \App\Models\Nota::where('asignatura_id', $asignaturaId)->get();
                }
            }
            return view('notas.index', compact('asignaturas', 'estudiantes', 'notas'));
        } elseif ($user->hasRole('estudiante')) {
            $notas = $user->notas()->with('asignatura')->get();
            return view('notas.index', compact('notas'));
        } else {
            abort(403);
        }
    }
} 