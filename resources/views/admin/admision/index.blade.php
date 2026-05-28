@extends('layouts.app')
@section('title', 'Admisión')
@section('header', 'Proceso de Admisión Final')

@section('content')
<div class="card">
    <div class="card-header"><h2>Ejecutar Proceso de Admisión</h2></div>
    <div class="card-body">
        <div class="alert alert-warning">
            ⚠️ Este proceso evalúa TODOS los postulantes de la gestión seleccionada, verifica aprobación en todas las materias (≥60), y asigna cupos por carrera considerando primera y segunda opción.
        </div>
        <form method="POST" action="{{ route('admin.admision.procesar') }}" onsubmit="return confirm('¿Ejecutar el proceso de admisión? Esto recalculará todos los resultados.')">
            @csrf
            <div class="form-group" style="max-width:300px;">
                <label class="form-label">Seleccione la Gestión</label>
                <select name="id_gestion" class="form-control" required>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id_gestion }}">{{ $g->nombreCompleto }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">🚀 Procesar Admisión</button>
        </form>
    </div>
</div>
@endsection
