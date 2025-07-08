<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'codigo'];

    public function docentes()
    {
        return $this->belongsToMany(User::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }
} 