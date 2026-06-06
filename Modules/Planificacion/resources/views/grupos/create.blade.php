@extends('layouts.app')
@section('title', isset($grupo) ? 'Editar Grupo' : 'Nuevo Grupo')
@section('header', isset($grupo) ? 'Editar Grupo' : 'Nuevo Grupo')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($grupo) ? route('admin.grupos.update', $grupo->id_grupo) : route('admin.grupos.store') }}">
            @csrf
            @if(isset($grupo)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label">Nombre del Grupo</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $grupo->nombre ?? '') }}" placeholder="Ej: Grupo A-1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Capacidad Máxima (máx. 70)</label>
                <input type="number" name="capacidad_maxima" class="form-control" value="{{ old('capacidad_maxima', $grupo->capacidad_maxima ?? 70) }}" min="1" max="70" required>
            </div>
            <div class="form-group">
                <label class="form-label">Turno</label>
                <select name="id_turno" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach($turnos as $turno)
                        <option value="{{ $turno->id_turno }}" {{ old('id_turno', $grupo->id_turno ?? '') == $turno->id_turno ? 'selected' : '' }}>{{ $turno->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Horario</label>
                <select name="id_horario" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach($horarios as $horario)
                        <option value="{{ $horario->id_horario }}" {{ old('id_horario', $grupo->id_horario ?? '') == $horario->id_horario ? 'selected' : '' }}>{{ $horario->rango }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Aula</label>
                <select name="id_aula" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach($aulas as $aula)
                        <option value="{{ $aula->id_aula }}" {{ old('id_aula', $grupo->id_aula ?? '') == $aula->id_aula ? 'selected' : '' }}>{{ $aula->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">{{ isset($grupo) ? 'Actualizar' : 'Crear' }}</button>
                <a href="{{ route('admin.grupos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
