<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @role('docente')
                        <h3 class="font-bold mb-2">Registrar Nota</h3>
                        <form method="POST" action="{{ route('notas.store') }}">
                            @csrf
                            <div class="mb-2">
                                <label>Estudiante ID:</label>
                                <input type="number" name="estudiante_id" class="border rounded p-1" required>
                            </div>
                            <div class="mb-2">
                                <label>Asignatura ID:</label>
                                <input type="number" name="asignatura_id" class="border rounded p-1" required>
                            </div>
                            <div class="mb-2">
                                <label>Nota 1:</label>
                                <input type="number" step="0.01" name="nota_1" class="border rounded p-1" required>
                            </div>
                            <div class="mb-2">
                                <label>Nota 2:</label>
                                <input type="number" step="0.01" name="nota_2" class="border rounded p-1" required>
                            </div>
                            <div class="mb-2">
                                <label>Nota 3:</label>
                                <input type="number" step="0.01" name="nota_3" class="border rounded p-1" required>
                            </div>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Registrar</button>
                        </form>
                    @endrole

                    @role('estudiante')
                        <h3 class="font-bold mb-2">Mis Notas</h3>
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th>Asignatura</th>
                                    <th>Nota 1</th>
                                    <th>Nota 2</th>
                                    <th>Nota 3</th>
                                    <th>Promedio</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (\App\Models\Nota::where('estudiante_id', auth()->id())->get() as $nota)
                                    <tr>
                                        <td>{{ $nota->asignatura->nombre ?? $nota->asignatura_id }}</td>
                                        <td>{{ $nota->nota_1 }}</td>
                                        <td>{{ $nota->nota_2 }}</td>
                                        <td>{{ $nota->nota_3 }}</td>
                                        <td>{{ $nota->promedio }}</td>
                                        <td>{{ $nota->estado_final }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endrole

                    @role('admin')
                        <h3 class="font-bold mb-2">Panel de administración</h3>
                        <p>Acceso total al sistema.</p>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
