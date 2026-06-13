@extends('layouts.app')
@section('title', 'Alumnos y Notas')
@section('header', 'Alumnos y Notas: ' . $materia->nombre)
@section('header-actions')
    <a href="{{ route('docente.dashboard') }}" class="btn btn-secondary btn-sm">Volver al Panel</a>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h3 style="color: var(--accent); margin-bottom: 8px;">Información de la Materia</h3>
        <p style="margin-bottom: 4px;"><strong>Grupo:</strong> {{ $grupo->nombre }}</p>
        <p style="margin-bottom: 4px;"><strong>Aula:</strong> {{ $grupo->aula->descripcion ?? '—' }} ({{ $grupo->aula->nombre ?? '—' }})</p>
        <p style="margin-bottom: 0;"><strong>Horario:</strong> {{ $grupo->horario->rango ?? '—' }} — {{ $grupo->turno->nombre ?? '—' }}</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Listado de Estudiantes Inscritos</h2>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>CI</th>
                        <th style="text-align: center;">Parcial 1</th>
                        <th style="text-align: center;">Parcial 2</th>
                        <th style="text-align: center;">Examen Final</th>
                        <th style="text-align: center;">Nota Final</th>
                        <th style="text-align: center;">Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($postulantes as $p)
                        @php
                            $nota = $p->notas->first();
                        @endphp
                        <tr>
                            <td><strong>{{ $p->usuario->nombreCompleto }}</strong></td>
                            <td>{{ $p->usuario->ci }}</td>
                            <td style="text-align: center;">{{ $nota && $nota->examen1 !== null ? $nota->examen1 : '—' }}</td>
                            <td style="text-align: center;">{{ $nota && $nota->examen2 !== null ? $nota->examen2 : '—' }}</td>
                            <td style="text-align: center;">{{ $nota && $nota->examen3 !== null ? $nota->examen3 : '—' }}</td>
                            <td style="text-align: center;">
                                <strong>{{ $nota && $nota->promedio !== null ? $nota->promedio : '—' }}</strong>
                            </td>
                            <td style="text-align: center;">
                                @if($nota && $nota->estado)
                                    <span class="badge {{ $nota->estado === 'APROBADO' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $nota->estado }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">Sin nota</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('docente.estudiantes.materia.nota', [$p->id_postulante, $materia->id_materia]) }}" class="btn btn-secondary btn-sm">
                                    Registrar Notas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay estudiantes inscritos en este grupo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
