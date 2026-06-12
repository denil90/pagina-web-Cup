@extends('layouts.app')
@section('title', 'Mi Postulación Docente')
@section('header', 'Panel del Docente')

@section('content')
<div class="fade-in">
    {{-- Status Card --}}
    <div class="docente-status-card">
        <h2>Estado de su Postulación</h2>

        @if($docente->estado === 'PENDIENTE')
            <div class="status-indicator status-indicator--pending mt-2">
                <span class="dot"></span>
                Pendiente de Verificación
            </div>
            <p class="mt-2">Su postulación está siendo revisada por un administrador. Asegúrese de subir todos los documentos requeridos.</p>
        @elseif($docente->estado === 'ACTIVO')
            <div class="status-indicator status-indicator--active mt-2">
                <span class="dot"></span>
                Aprobado — Docente Activo
            </div>
            <p class="mt-2">¡Felicidades! Su postulación ha sido aprobada. A continuación puede visualizar sus grupos asignados y registrar calificaciones.</p>
        @elseif($docente->estado === 'RECHAZADO')
            <div class="status-indicator status-indicator--rejected mt-2">
                <span class="dot"></span>
                Postulación Rechazada
            </div>
            <p class="mt-2">Lamentablemente su postulación fue rechazada para esta gestión. Podrá volver a postular en la siguiente gestión académica.</p>
        @else
            <div class="status-indicator status-indicator--pending mt-2">
                <span class="dot"></span>
                {{ $docente->estado }}
            </div>
        @endif
    </div>

    {{-- Info Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👤</div>
            <div>
                <div class="stat-value">{{ $usuario->nombreCompleto }}</div>
                <div class="stat-label">Nombre Completo</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎓</div>
            <div>
                <div class="stat-value" style="font-size: 1rem;">{{ $docente->titulo_profesional }}</div>
                <div class="stat-label">Título Profesional</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📧</div>
            <div>
                <div class="stat-value" style="font-size: 1rem;">{{ $usuario->correo }}</div>
                <div class="stat-label">Correo Electrónico</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div>
                @php
                    $docs = collect(['archivo_titulo', 'archivo_maestria', 'archivo_diplomado'])
                        ->filter(fn($f) => $docente->$f)->count();
                @endphp
                <div class="stat-value">{{ $docs }} / 3</div>
                <div class="stat-label">Documentos Subidos</div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    @if($docente->estado === 'PENDIENTE')
        <div class="card">
            <div class="card-header">
                <h2>📁 Documentos Requeridos</h2>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Suba los documentos de respaldo para agilizar la revisión de su postulación.</p>
                <a href="{{ route('docente.requisitos') }}" class="btn btn-primary">Gestionar Documentos</a>
            </div>
        </div>
    @endif

    @if($docente->estado === 'ACTIVO')
        {{-- Show assigned groups if any --}}
        @php $asignaciones = $docente->gruposAsignados()->with(['grupo.horario', 'materia'])->get(); @endphp
        @if($asignaciones->isNotEmpty())
            <div class="card mt-3">
                <div class="card-header">
                    <h2>📚 Grupos Asignados</h2>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Grupo</th>
                                    <th>Materia</th>
                                    <th>Horario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($asignaciones as $a)
                                    <tr>
                                        <td>{{ $a->grupo->nombre ?? '—' }}</td>
                                        <td>{{ $a->materia->nombre ?? '—' }}</td>
                                        <td>{{ $a->grupo->horario->rango ?? '—' }}</td>
                                        <td>
                                            <a href="{{ route('docente.grupos.materia.estudiantes', [$a->id_grupo, $a->id_materia]) }}" class="btn btn-primary btn-sm">
                                                📝 Alumnos y Notas
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
