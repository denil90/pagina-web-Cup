@extends('layouts.app')
@section('title', 'Registrar Notas')
@section('header', 'Registrar Notas: ' . $materia->nombre)

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2>Registrar Calificaciones</h2>
    </div>
    <div class="card-body">
        <div style="background: var(--bg-secondary); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); margin-bottom: 24px;">
            <p style="margin-bottom: 6px;"><strong>Estudiante:</strong> {{ $postulante->usuario->nombreCompleto }}</p>
            <p style="margin-bottom: 6px;"><strong>CI:</strong> {{ $postulante->usuario->ci }}</p>
            <p style="margin-bottom: 0;"><strong>Materia:</strong> {{ $materia->nombre }}</p>
        </div>

        <form method="POST" action="{{ route('docente.estudiantes.materia.nota.store') }}">
            @csrf
            
            <input type="hidden" name="id_postulante" value="{{ $postulante->id_postulante }}">
            <input type="hidden" name="id_materia" value="{{ $materia->id_materia }}">

            <div class="form-group mb-3">
                <label class="form-label">Parcial 1 (0 - 100)</label>
                <input type="number" step="0.01" name="examen1" class="form-control" 
                       value="{{ old('examen1', $nota ? $nota->examen1 : '') }}" 
                       min="0" max="100" placeholder="Ej: 85.50">
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Parcial 2 (0 - 100)</label>
                <input type="number" step="0.01" name="examen2" class="form-control" 
                       value="{{ old('examen2', $nota ? $nota->examen2 : '') }}" 
                       min="0" max="100" placeholder="Ej: 90.00">
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Examen Final (0 - 100)</label>
                <input type="number" step="0.01" name="examen3" class="form-control" 
                       value="{{ old('examen3', $nota ? $nota->examen3 : '') }}" 
                       min="0" max="100" placeholder="Ej: 78.50">
            </div>

            <div class="btn-group w-100">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar Calificaciones</button>
                <a href="{{ route('docente.grupos.materia.estudiantes', [$postulante->id_grupo, $materia->id_materia]) }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
