<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Asignatura;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles
        Role::firstOrCreate(['name' => 'docente']);
        Role::firstOrCreate(['name' => 'estudiante']);
        Role::firstOrCreate(['name' => 'admin']);

        // Crear usuario docente
        $docente = User::create([
            'name' => 'Docente Ejemplo',
            'email' => 'docente@gmail.com',
            'password' => bcrypt('password'),
            'estado' => 'activo',
        ]);
        $docente->assignRole('docente');

        // Crear usuario estudiante
        $estudiante = User::create([
            'name' => 'Estudiante Ejemplo',
            'email' => 'estudiante@gmail.com',
            'password' => bcrypt('password'),
            'estado' => 'activo',
        ]);
        $estudiante->assignRole('estudiante');

        // Crear usuario admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'estado' => 'activo',
        ]);
        $admin->assignRole('admin');

        // Crear asignatura
        $asignatura = Asignatura::create([
            'nombre' => 'Matemáticas',
            'codigo' => 'MAT101',
        ]);
        // Relacionar docente con asignatura
        $docente->asignaturas()->attach($asignatura->id);
    }
}
