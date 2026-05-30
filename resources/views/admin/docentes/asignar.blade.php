@extends('layouts.app')
@section('title', 'Asignar Docente')
@section('header', 'Asignar Grupos - ' . $docente->usuario->nombreCompleto)

@section('content')
<div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
    <div class="stat-card">
        <div class="stat-icon"></div>
        <div>
            <div class="stat-value">{{ $carga['total_grupos'] }}</div>
            <div class="stat-label">Grupos Asignados (máx. 5)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"></div>
        <div>
            <div class="stat-value">{{ $carga['asignaciones']->count() }}</div>
            <div class="stat-label">Materias Asignadas</div>
        </div>
    </div>
</div>

{{-- Formulario de asignación --}}
<div class="card mb-3">
    <div class="card-header"><h2>Nueva Asignación</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.docentes.asignar.store', $docente->id_docente) }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Grupo</label>
                    <select name="id_grupo" class="form-control" required>
                        <option value="">Seleccione un grupo...</option>
                        @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id_grupo }}">
                                {{ $grupo->nombre }} — {{ $grupo->turno?->nombre ?? 'Sin turno' }} ({{ $grupo->horario?->rango ?? 'Sin horario' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Materia</label>
                    <select name="id_materia" class="form-control" required>
                        <option value="">Seleccione una materia...</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id_materia }}">{{ $materia->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Asignar</button>
        </form>
    </div>
</div>

{{-- Lista de asignaciones actuales --}}
<div class="card">
    <div class="card-header"><h2>Asignaciones Actuales</h2></div>
    <div class="card-body">
        @if($carga['asignaciones']->isEmpty())
            <div class="empty-state">
                <div class="icon"></div>
                <h3>Sin asignaciones</h3>
            </div>
        @else
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Grupo</th>
                            <th>Materia</th>
                            <th>Turno</th>
                            <th>Horario</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carga['asignaciones'] as $asig)
                            <tr>
                                <td>{{ $asig->grupo->nombre }}</td>
                                <td><span class="badge badge-primary">{{ $asig->materia->nombre }}</span></td>
                                <td>{{ $asig->grupo->turno?->nombre ?? '—' }}</td>
                                <td>{{ $asig->grupo->horario?->rango ?? '—' }}</td>
                                <td>
                                    <form action="{{ route('admin.docentes.asignacion.destroy', $asig->id) }}" method="POST" onsubmit="return confirm('¿Remover?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
