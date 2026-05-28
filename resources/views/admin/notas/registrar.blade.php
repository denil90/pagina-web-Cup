@extends('layouts.app')
@section('title', 'Registrar Nota')
@section('header', 'Registrar Nota: ' . $postulante->usuario->nombreCompleto . ' — ' . $materia->nombre)

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-body">
        <div class="alert alert-info">
            Porcentajes: Ex.1 = {{ $materia->porcentaje_examen1 }}%, Ex.2 = {{ $materia->porcentaje_examen2 }}%, Final = {{ $materia->porcentaje_examen3 }}%.
            El promedio se calcula automáticamente por el trigger de la BD.
        </div>

        <form method="POST" action="{{ route('admin.notas.guardar') }}">
            @csrf
            <input type="hidden" name="id_postulante" value="{{ $postulante->id_postulante }}">
            <input type="hidden" name="id_materia" value="{{ $materia->id_materia }}">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Examen 1 ({{ $materia->porcentaje_examen1 }}%)</label>
                    <input type="number" name="examen1" class="form-control" value="{{ old('examen1', $nota->examen1 ?? '') }}" min="0" max="100" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Examen 2 ({{ $materia->porcentaje_examen2 }}%)</label>
                    <input type="number" name="examen2" class="form-control" value="{{ old('examen2', $nota->examen2 ?? '') }}" min="0" max="100" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Examen Final ({{ $materia->porcentaje_examen3 }}%)</label>
                    <input type="number" name="examen3" class="form-control" value="{{ old('examen3', $nota->examen3 ?? '') }}" min="0" max="100" step="0.01">
                </div>
            </div>

            @if($nota)
                <div class="alert alert-info mt-2">
                    Promedio actual: <strong>{{ $nota->promedio }}</strong> — Estado: <span class="badge {{ $nota->estado === 'APROBADO' ? 'badge-success' : 'badge-danger' }}">{{ $nota->estado }}</span>
                </div>
            @endif

            <div class="btn-group mt-2">
                <button type="submit" class="btn btn-primary">Guardar Notas</button>
                <a href="{{ route('admin.notas.index', ['id_grupo' => $postulante->id_grupo]) }}" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>
</div>
@endsection
