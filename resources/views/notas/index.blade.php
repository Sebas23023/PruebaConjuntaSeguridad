@extends('layouts.app')

@section('title', 'Notas')

@section('content')
@role('docente')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Notas por Asignatura</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form method="GET" action="{{ route('notas.index') }}" class="form-inline mb-3">
                <label for="asignatura_id" class="mr-2">Selecciona una asignatura:</label>
                <select name="asignatura_id" id="asignatura_id" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="">-- Selecciona --</option>
                    @foreach($asignaturas as $asignatura)
                        <option value="{{ $asignatura->id }}" {{ request('asignatura_id') == $asignatura->id ? 'selected' : '' }}>{{ $asignatura->nombre }}</option>
                    @endforeach
                </select>
            </form>
            @if(isset($estudiantes) && count($estudiantes) > 0)
                <table class="table table-bordered table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Estudiante</th>
                            <th>Nota 1</th>
                            <th>Nota 2</th>
                            <th>Nota 3</th>
                            <th>Promedio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estudiantes as $estudiante)
                            @php
                                $nota = $notas->where('estudiante_id', $estudiante->id)->first();
                            @endphp
                            <tr>
                                <td>{{ $estudiante->name }}</td>
                                <td>{{ $nota->nota_1 ?? '-' }}</td>
                                <td>{{ $nota->nota_2 ?? '-' }}</td>
                                <td>{{ $nota->nota_3 ?? '-' }}</td>
                                <td>{{ $nota->promedio ?? '-' }}</td>
                                <td>
                                    @if(isset($nota->estado_final))
                                        <span class="badge {{ $nota->estado_final == 'aprobado' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($nota->estado_final) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <!-- Botón para registrar/modificar nota -->
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#notaModal{{ $estudiante->id }}">
                                        <i class="fas fa-pen"></i> Registrar/Editar
                                    </button>
                                    <!-- Modal para registrar/modificar nota -->
                                    <div class="modal fade" id="notaModal{{ $estudiante->id }}" tabindex="-1" role="dialog" aria-labelledby="notaModalLabel{{ $estudiante->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ isset($nota) ? route('notas.update', $nota->id) : route('notas.store') }}" method="POST">
                                                    @csrf
                                                    @if(isset($nota))
                                                        @method('PUT')
                                                    @endif
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="notaModalLabel{{ $estudiante->id }}">Registrar/Editar Nota</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="estudiante_id" value="{{ $estudiante->id }}">
                                                        <input type="hidden" name="asignatura_id" value="{{ request('asignatura_id') }}">
                                                        <div class="form-group">
                                                            <label>Nota 1</label>
                                                            <input type="number" step="0.01" name="nota_1" class="form-control" value="{{ $nota->nota_1 ?? '' }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Nota 2</label>
                                                            <input type="number" step="0.01" name="nota_2" class="form-control" value="{{ $nota->nota_2 ?? '' }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Nota 3</label>
                                                            <input type="number" step="0.01" name="nota_3" class="form-control" value="{{ $nota->nota_3 ?? '' }}" required>
                                                        </div>
                                                        @if(isset($nota))
                                                        <div class="form-group">
                                                            <label>Motivo de edición</label>
                                                            <input type="text" name="motivo" class="form-control" required>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif(request('asignatura_id'))
                <div class="alert alert-info">No hay estudiantes en esta asignatura.</div>
            @endif
        </div>
    </div>
@endrole

@role('estudiante')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Mis Notas</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover table-striped">
                <thead class="thead-dark">
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
                    @forelse($notas as $nota)
                        <tr>
                            <td>{{ $nota->asignatura->nombre ?? $nota->asignatura_id }}</td>
                            <td>{{ $nota->nota_1 }}</td>
                            <td>{{ $nota->nota_2 }}</td>
                            <td>{{ $nota->nota_3 }}</td>
                            <td>{{ $nota->promedio }}</td>
                            <td>
                                <span class="badge {{ $nota->estado_final == 'aprobado' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($nota->estado_final) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No tienes notas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endrole
@endsection 