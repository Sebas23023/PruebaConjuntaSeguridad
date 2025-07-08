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
} 