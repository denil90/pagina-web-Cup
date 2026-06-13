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
        <div class="card-header"><h2>Carreras y Grupo</h2></div>
        <div class="card-body">
            <p><strong>1ª Opción:</strong> {{ $postulante->carreraPrimera?->nombre ?? '—' }}</p>
            <p><strong>2ª Opción:</strong> {{ $postulante->carreraSegunda?->nombre ?? 'No seleccionada' }}</p>
            <p><strong>Turno Preferido:</strong> {{ $postulante->turnoPreferido?->nombre ?? '—' }}</p>
            <p><strong>Grupo:</strong> {{ $postulante->grupo?->nombre ?? 'Pendiente de asignación' }}</p>
            @if($postulante->grupo)
                <p><strong>Turno:</strong> {{ $postulante->grupo->turno?->nombre ?? '—' }}</p>
                <p><strong>Aula:</strong> {{ $postulante->grupo->aula?->nombre ?? '—' }} - {{ $postulante->grupo->aula?->edificio ?? '' }}</p>
            @endif
        </div>
    </div>
</div>

@if($postulante->grupo && $postulante->grupo->docenteGrupos->isNotEmpty())
<div class="card mt-3">
    <div class="card-header"><h2>Tu Horario de Clases</h2></div>
    <div class="card-body">
        <p style="margin-bottom: 12px; color: var(--text-secondary);">
            Grupo <strong>{{ $postulante->grupo->nombre }}</strong> — Turno {{ $postulante->grupo->turno?->nombre ?? '' }} — {{ $postulante->grupo->aula?->nombre ?? '' }}
        </p>
        <table class="table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Materia</th>
                    <th>Docente</th>
                </tr>
            </thead>
            <tbody>
                @foreach($postulante->grupo->docenteGrupos->sortBy(fn($dg) => $dg->horario?->hora_inicio) as $dg)
                <tr>
                    <td>{{ $dg->horario ? substr($dg->horario->hora_inicio, 0, 5) . ' - ' . substr($dg->horario->hora_final, 0, 5) : '—' }}</td>
                    <td><strong>{{ $dg->materia?->nombre ?? '—' }}</strong></td>
                    <td>{{ $dg->docente?->usuario?->nombre ?? '' }} {{ $dg->docente?->usuario?->apellidos ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p style="margin-top: 8px; font-size: 0.85rem; color: var(--text-secondary);">
            * El horario se repite de Lunes a Viernes.
        </p>
    </div>
</div>
@endif

@if($postulante->admisionFinal)
<div class="card mt-3">
    <div class="card-body" style="text-align:center;">
        <h2 style="color:var(--success-light);font-size:1.5rem;">¡Felicidades! Has sido ADMITIDO</h2>
        <p style="font-size:1.1rem;">Carrera: <strong>{{ $postulante->admisionFinal->carrera->nombre }}</strong></p>
        <p>Nota Final CUP: <strong>{{ $postulante->admisionFinal->nota_final_cup }}</strong> | {{ $postulante->admisionFinal->opcion_ingreso }}</p>
    </div>
</div>
@endif
@endsection
