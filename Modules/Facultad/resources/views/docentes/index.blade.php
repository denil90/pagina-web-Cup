@extends('layouts.app')
@section('title', 'Docentes')
@section('header', 'Plantel Docente')
@section('header-actions')
    <a href="{{ route('admin.docentes.create') }}" class="btn btn-primary btn-sm">+ Nuevo Docente</a>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.docentes.index') }}" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div>
                <label for="estado" class="form-label" style="margin-bottom: 0;">Filtrar por Estado:</label>
            </div>
            <div style="min-width: 150px;">
                <select name="estado" id="estado" class="form-control" onchange="this.form.submit()" style="padding: 6px 12px;">
                    <option value="">Todos</option>
                    <option value="PENDIENTE" {{ request('estado') === 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                    <option value="ACTIVO" {{ request('estado') === 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                    <option value="RECHAZADO" {{ request('estado') === 'RECHAZADO' ? 'selected' : '' }}>Rechazado</option>
                    <option value="INACTIVO" {{ request('estado') === 'INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            @if(request()->filled('estado'))
                <div>
                    <a href="{{ route('admin.docentes.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
                </div>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>CI</th>
                        <th>Título</th>
                        <th>Maestría</th>
                        <th>Diplomado</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($docentes as $docente)
                        <tr>
                            <td><strong>{{ $docente->usuario->nombreCompleto }}</strong></td>
                            <td>{{ $docente->usuario->ci }}</td>
                            <td>{{ $docente->titulo_profesional }}</td>
                            <td>{{ $docente->maestria ?? '—' }}</td>
                            <td>{{ $docente->diplomado ?? '—' }}</td>
                            <td>
                                @if($docente->estado === 'ACTIVO')
                                    <span class="badge badge-success">ACTIVO</span>
                                @elseif($docente->estado === 'PENDIENTE')
                                    <span class="badge badge-warning">PENDIENTE</span>
                                @elseif($docente->estado === 'RECHAZADO')
                                    <span class="badge badge-danger">RECHAZADO</span>
                                @else
                                    <span class="badge badge-secondary">{{ $docente->estado }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.docentes.show', $docente->id_docente) }}" class="btn btn-primary btn-sm">Ver Detalle</a>
                                    @if($docente->estado === 'ACTIVO')
                                        <a href="{{ route('admin.docentes.asignar', $docente->id_docente) }}" class="btn btn-info btn-sm">Asignar Grupos</a>
                                    @endif
                                    <a href="{{ route('admin.docentes.edit', $docente->id_docente) }}" class="btn btn-secondary btn-sm">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No hay docentes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
