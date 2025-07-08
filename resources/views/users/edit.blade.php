@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Editar Usuario') }}
                    </h2>

                    @if ($errors->any())
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form class="space-y-6" action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <h5 class="text-xl font-medium text-gray-900">Información del Usuario</h5>

                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nombre de Usuario</label>
                            <input type="text" name="name" id="name" value="{{ $user->name }}" required
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>

                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email del Usuario</label>
                            <input type="email" name="email" id="email" value="{{ $user->email }}" required
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" readonly>
                        </div>

                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                            <input type="password" name="password" id="password" placeholder="••••••••"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        
                        <div>
                            <label for="role" class="block mb-2 text-sm font-medium text-gray-900">Rol</label>
                            <select name="roles[]" id="role" multiple required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Estado</label>
                            <div class="flex items-center">
                                <input type="radio" id="status_active" name="status" value="active" {{ $user->status === 'active' ? 'checked' : '' }} class="mr-2">
                                <label for="status_active" class="mr-4">Activo</label>
                                <input type="radio" id="status_inactive" name="status" value="inactive" {{ $user->status === 'inactive' ? 'checked' : '' }} class="mr-2">
                                <label for="status_inactive">Inactivo</label>
                            </div>
                        </div>
                        
                        @if($user->status === 'inactive')
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Motivo de desactivación anterior</label>
                            <p class="bg-gray-100 p-3 rounded">{{ $user->deactivation_reason }}</p>
                        </div>
                        @endif
                        
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
