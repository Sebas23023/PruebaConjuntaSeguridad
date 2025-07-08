<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    

    public function index()
    {
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@espe\.edu\.ec$/'
            ],
            'password' => 'required|min:6|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ], [
            'email.regex' => 'El correo debe ser institucional y terminar en @espe.edu.ec.',
            'email.unique' => 'Este correo ya está registrado.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        $user->syncRoles($request->roles);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties([
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')
            ])
            ->log('Usuario creado');

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|min:6|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'name' => $request->name,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Si el estado cambia a inactivo, registrar el motivo
        if ($request->status === 'inactive' && $user->status !== 'inactive') {
            $data['deactivation_reason'] = 'Cambiado por administrador';
            $data['deleted_by'] = Auth::id();
        }

        $user->update($data);
        $user->syncRoles($request->roles);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties([
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'roles' => $user->roles->pluck('name')
            ])
            ->log('Usuario actualizado');

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        $user->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->log('Usuario eliminado');

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente');
    }

    public function deactivateUser(Request $request, $userId)
{
    $request->validate([
        'reason' => 'required|string|max:255',
    ]);

    $user = User::findOrFail($userId);
    $admin = Auth::user();

    // Actualizar el estado y motivo
    $user->update([
        'status' => 'inactive',
        'deactivation_reason' => $request->reason,
        'deleted_by' => $admin->id,
    ]);

    // Cerrar sesión del usuario si está conectado
    if ($user->id === Auth::id()) {
        Auth::logout();
    } else {
        // Invalidar todas las sesiones del usuario desactivado
        $this->logoutFromAllDevices($user);
    }

    activity()
        ->causedBy($admin)
        ->performedOn($user)
        ->withProperties([
            'reason' => $request->reason,
            'status' => 'inactive',
        ])
        ->log('Usuario desactivado');

    return redirect()->back()->with('success', 'Usuario desactivado exitosamente.');
}

    public function activateUser($userId)
    {
        $user = User::findOrFail($userId);
        $admin = Auth::user();

        $user->update([
            'status' => 'active',
            'deactivation_reason' => null,
            'deleted_by' => null,
        ]);

        activity()
            ->causedBy($admin)
            ->performedOn($user)
            ->withProperties([
                'status' => 'active',
            ])
            ->log('Usuario activado');

        return redirect()->back()->with('success', 'Usuario activado exitosamente.');
    }

    protected function logoutFromAllDevices(User $user)
    {
        // Eliminar el remember_token
        $user->setRememberToken(null);
        $user->save();
        
        // Si estás almacenando sesiones en base de datos
        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->delete();
        }
    }
}