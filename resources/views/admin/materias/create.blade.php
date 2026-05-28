@extends('layouts.app')
@section('title', isset($materia) ? 'Editar Materia' : 'Nueva Materia')
@section('header', isset($materia) ? 'Editar Materia' : 'Nueva Materia')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($materia) ? route('admin.materias.update', $materia->id_materia) : route('admin.materias.store') }}">
            @csrf
            @if(isset($materia)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label">Nombre de la Materia</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $materia->nombre ?? '') }}" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Examen 1 (%)</label>
                    <input type="number" name="porcentaje_examen1" class="form-control" value="{{ old('porcentaje_examen1', $materia->porcentaje_examen1 ?? 30) }}" step="0.01" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Examen 2 (%)</label>
                    <input type="number" name="porcentaje_examen2" class="form-control" value="{{ old('porcentaje_examen2', $materia->porcentaje_examen2 ?? 30) }}" step="0.01" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Examen Final (%)</label>
                    <input type="number" name="porcentaje_examen3" class="form-control" value="{{ old('porcentaje_examen3', $materia->porcentaje_examen3 ?? 40) }}" step="0.01" min="0" max="100" required>
                </div>
            </div>
            <p class="text-muted" style="font-size: 0.8rem;">Los porcentajes deben sumar exactamente 100%.</p>
            <div class="btn-group mt-2">
                <button type="submit" class="btn btn-primary">{{ isset($materia) ? 'Actualizar' : 'Guardar' }}</button>
                <a href="{{ route('admin.materias.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
