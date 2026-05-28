@extends('layouts.app')
@section('title', 'Grupos')
@section('header', 'Grupos del CUP')
@section('header-actions')
    <a href="{{ route('admin.grupos.create') }}" class="btn btn-primary btn-sm">+ Nuevo Grupo</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Grupo</th><th>Turno</th><th>Horario</th><th>Aula</th><th>Capacidad</th><th>Inscritos</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse($grupos as $grupo)
                        <tr>
                            <td><strong>{{ $grupo->nombre }}</strong></td>
                            <td><span class="badge badge-info">{{ $grupo->turno?->nombre ?? '—' }}</span></td>
                            <td>{{ $grupo->horario?->rango ?? '—' }}</td>
                            <td>{{ $grupo->aula?->descripcion ?? '—' }}</td>
                            <td>{{ $grupo->capacidad_maxima }}</td>
                            <td>
                                <span class="badge {{ $grupo->postulantes_count >= $grupo->capacidad_maxima ? 'badge-danger' : 'badge-success' }}">
                                    {{ $grupo->postulantes_count }}/{{ $grupo->capacidad_maxima }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.grupos.show', $grupo->id_grupo) }}" class="btn btn-info btn-sm">Ver</a>
                                    <a href="{{ route('admin.grupos.edit', $grupo->id_grupo) }}" class="btn btn-secondary btn-sm">Editar</a>
                                    <form action="{{ route('admin.grupos.destroy', $grupo->id_grupo) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No hay grupos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
