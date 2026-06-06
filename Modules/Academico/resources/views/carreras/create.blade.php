@extends('layouts.app')
@section('title', isset($carrera) ? 'Editar Carrera' : 'Nueva Carrera')
@section('header', isset($carrera) ? 'Editar Carrera' : 'Nueva Carrera')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($carrera) ? route('admin.carreras.update', $carrera->id) : route('admin.carreras.store') }}">
            @csrf
            @if(isset($carrera)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label">Nombre de la Carrera</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $carrera->nombre ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control">{{ old('descripcion', $carrera->descripcion ?? '') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Cupo Máximo de Admitidos</label>
                <input type="number" name="cupo_maximo" class="form-control" value="{{ old('cupo_maximo', $carrera->cupo_maximo ?? 100) }}" min="1" required>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">{{ isset($carrera) ? 'Actualizar' : 'Guardar' }}</button>
                <a href="{{ route('admin.carreras.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
