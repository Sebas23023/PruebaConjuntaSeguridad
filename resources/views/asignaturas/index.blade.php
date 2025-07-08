@extends('layouts.app')

@section('title', 'Mis Asignaturas')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Mis Asignaturas</h3>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered table-hover table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asignaturas as $asignatura)
                    <tr>
                        <td>{{ $asignatura->nombre }}</td>
                        <td>{{ $asignatura->codigo }}</td>
                        <td>
                            <a href="{{ route('notas.index', ['asignatura_id' => $asignatura->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-users"></i> Ver Estudiantes y Notas
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No tienes asignaturas asignadas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 