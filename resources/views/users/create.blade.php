@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Crear Nuevo Usuario') }}
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

                    <form class="space-y-6" action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <h5 class="text-xl font-medium text-gray-900">Información del Usuario</h5>

                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nombre de Usuario</label>
                            <input type="text" name="name" id="name" placeholder="Nombre Apellido" required
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>

                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email del Usuario</label>
                            <input type="email" name="email" id="email" placeholder="correo@espe.edu.ec" required
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>

                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Contraseña</label>
                            <input type="password" name="password" id="password" placeholder="••••••••" required
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        
                        <div>
                            <label for="role" class="block mb-2 text-sm font-medium text-gray-900">Rol</label>
                            <select name="roles[]" id="role" multiple required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Mantén presionado Ctrl para seleccionar múltiples roles</p>
                        </div>
                        
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
