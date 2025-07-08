<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Administrador', 'check.user.status']);
    }

    // Listar todos los usuarios
    public function index()
    {
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
        ]);
        
        \DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'estado' => 'activo',
            ]);
            $user->syncRoles($request->roles);
            \DB::commit();
            return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear usuario: ' . $e->getMessage()]);
        }
    }

    // Mostrar formulario de edición
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    // Actualizar usuario
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
        ]);
        
        \DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
            $user->syncRoles($request->roles);
            \DB::commit();
            return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar usuario: ' . $e->getMessage()]);
        }
    }

    // Eliminar usuario
    public function destroy(User $user)
    {
        if (Auth::id() == $user->id) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }
        \DB::beginTransaction();
        try {
            $user->delete();
            \DB::commit();
            return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar usuario: ' . $e->getMessage()]);
        }
    }

    // Inactivar usuario (solo estudiantes)
    public function inactivar(Request $request, User $user)
    {
        $request->validate([
            'motivo_inactivo' => 'required|string',
        ]);
        if (!$user->hasRole('estudiante')) {
            return redirect()->route('users.index')->with('error', 'Solo se puede inactivar estudiantes.');
        }
        $user->estado = 'inactivo';
        $user->motivo_inactivo = $request->motivo_inactivo;
        $user->save();
        return redirect()->route('users.index')->with('success', 'Usuario inactivado correctamente.');
    }

    // Activar usuario (solo estudiantes)
    public function activar(User $user)
    {
        if (!$user->hasRole('estudiante')) {
            return redirect()->route('users.index')->with('error', 'Solo se puede activar estudiantes.');
        }
        $user->estado = 'activo';
        $user->motivo_inactivo = null;
        $user->save();
        return redirect()->route('users.index')->with('success', 'Usuario activado correctamente.');
    }
} 