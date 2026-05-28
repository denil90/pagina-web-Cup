@extends('layouts.app')
@section('title', 'Editar Gestión')
@section('header', 'Editar Gestión')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.gestiones.update', $gestion->id_gestion) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Semestre</label>
                <select name="semestre" class="form-control" required>
                    <option value="1" {{ $gestion->semestre == '1' ? 'selected' : '' }}>Primer Semestre</option>
                    <option value="2" {{ $gestion->semestre == '2' ? 'selected' : '' }}>Segundo Semestre</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Año</label>
                <input type="number" name="anio" class="form-control" value="{{ $gestion->anio }}" min="2020" max="2050" required>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('admin.gestiones.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
