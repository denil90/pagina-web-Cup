@extends('layouts.app')
@section('title', 'Mi Panel')
@section('header', 'Panel del Postulante')

@section('content')
<div class="card mb-3">
    <div class="card-header"><h2>Estado de tu Inscripción</h2></div>
    <div class="card-body">
        <div class="progress-steps">
            @foreach($estadoInscripcion as $i => $paso)
                <div class="progress-step {{ $paso['completado'] ? 'completed' : ($i === collect($estadoInscripcion)->search(fn($p) => !$p['completado']) ? 'active' : '') }}">
                    <div class="step-circle">{{ $paso['completado'] ? '✓' : $i + 1 }}</div>
                    <span class="step-label">{{ $paso['nombre'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="form-row">
    <div class="card">
        <div class="card-header"><h2>Datos Personales</h2></div>
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $usuario->nombreCompleto }}</p>
            <p><strong>CI:</strong> {{ $usuario->ci }}</p>
            <p><strong>Correo:</strong> {{ $usuario->correo }}</p>
            <p><strong>Gestión:</strong> {{ $postulante->gestion?->nombreCompleto ?? '—' }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h2>Carreras Seleccionadas</h2></div>
        <div class="card-body">
            <p><strong>1ª Opción:</strong> {{ $postulante->carreraPrimera?->nombre ?? '—' }}</p>
            <p><strong>2ª Opción:</strong> {{ $postulante->carreraSegunda?->nombre ?? 'No seleccionada' }}</p>
            <p><strong>Grupo:</strong> {{ $postulante->grupo?->nombre ?? 'Pendiente de asignación' }}</p>
            @if($postulante->grupo)
                <p><strong>Turno:</strong> {{ $postulante->grupo->turno?->nombre ?? '—' }}</p>
                <p><strong>Horario:</strong> {{ $postulante->grupo->horario?->rango ?? '—' }}</p>
            @endif
        </div>
    </div>
</div>

@if($postulante->admisionFinal)
<div class="card mt-3">
    <div class="card-body" style="text-align:center;">
        <h2 style="color:var(--success-light);font-size:1.5rem;">🎉 ¡Felicidades! Has sido ADMITIDO</h2>
        <p style="font-size:1.1rem;">Carrera: <strong>{{ $postulante->admisionFinal->carrera->nombre }}</strong></p>
        <p>Nota Final CUP: <strong>{{ $postulante->admisionFinal->nota_final_cup }}</strong> | {{ $postulante->admisionFinal->opcion_ingreso }}</p>
    </div>
</div>
@endif
@endsection
