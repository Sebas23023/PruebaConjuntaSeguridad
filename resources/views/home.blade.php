@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="row">
    @role('Administrador')
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>Usuarios</h3>
                    <p>Gestión de usuarios y roles</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('users.index') }}" class="small-box-footer">Ir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    @endrole
    @role('docente')
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Mis Asignaturas</h3>
                    <p>Ver y gestionar asignaturas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book"></i>
                </div>
                <a href="{{ route('asignaturas.index') }}" class="small-box-footer">Ir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Notas</h3>
                    <p>Registrar y modificar notas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <a href="{{ route('notas.index') }}" class="small-box-footer">Ir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>Auditoría</h3>
                    <p>Ver historial de acciones</p>
                </div>
                <div class="icon">
                    <i class="fas fa-history"></i>
                </div>
                <a href="{{ route('auditorias.index') }}" class="small-box-footer">Ir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    @endrole
    @role('estudiante')
        <div class="col-md-4">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>Mis Notas</h3>
                    <p>Ver mis notas y promedios</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <a href="{{ route('notas.index') }}" class="small-box-footer">Ir <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    @endrole
</div>
@endsection 