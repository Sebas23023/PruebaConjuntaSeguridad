@extends('layouts.app')

@section('title', 'Auditoría del Sistema')

@section('content')
@role('Administrador')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Historial de Auditoría</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Motivo</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditorias as $auditoria)
                    <tr>
                        <td>{{ $auditoria->usuario->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($auditoria->accion) }}</td>
                        <td>{{ $auditoria->motivo }}</td>
                        <td>{{ $auditoria->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No hay registros de auditoría.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endrole
@endsection 