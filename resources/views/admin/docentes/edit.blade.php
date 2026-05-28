@extends('layouts.app')
@section('title', 'Editar Docente')
@section('header', 'Editar Docente: ' . $docente->usuario->nombreCompleto)

@section('content')
<div class="card" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.docentes.update', $docente->id_docente) }}">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nombres</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $docente->usuario->nombre }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" value="{{ $docente->usuario->apellidos }}" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Título Profesional</label>
                <input type="text" name="titulo_profesional" class="form-control" value="{{ $docente->titulo_profesional }}" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Maestría</label>
                    <input type="text" name="maestria" class="form-control" value="{{ $docente->maestria }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Diplomado</label>
                    <input type="text" name="diplomado" class="form-control" value="{{ $docente->diplomado }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="ACTIVO" {{ $docente->estado === 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                    <option value="INACTIVO" {{ $docente->estado === 'INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('admin.docentes.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
