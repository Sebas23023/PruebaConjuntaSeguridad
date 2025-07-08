<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

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
} 