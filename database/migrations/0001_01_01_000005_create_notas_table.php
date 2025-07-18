<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('asignatura_id')->constrained('asignaturas')->onDelete('cascade');
            $table->decimal('nota_1', 5, 2);
            $table->decimal('nota_2', 5, 2);
            $table->decimal('nota_3', 5, 2);
            $table->decimal('promedio', 5, 2);
            $table->string('estado_final');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
}; 