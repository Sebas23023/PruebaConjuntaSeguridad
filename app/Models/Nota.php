<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nota extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'estudiante_id',
        'asignatura_id',
        'nota_1',
        'nota_2',
        'nota_3',
        'promedio',
        'estado_final',
    ];

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class);
    }

    public function calcularPromedioYEstado()
    {
        $this->promedio = round((($this->nota_1 + $this->nota_2 + $this->nota_3) / 3), 2);
        $this->estado_final = $this->promedio >= 14.5 ? 'aprobado' : 'reprobado';
    }
} 