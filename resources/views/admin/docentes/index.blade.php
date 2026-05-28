@extends('layouts.app')
@section('title', 'Docentes')
@section('header', 'Plantel Docente')
@section('header-actions')
    <a href="{{ route('admin.docentes.create') }}" class="btn btn-primary btn-sm">+ Nuevo Docente</a>
@endsection

@section('content')
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
                                <span class="badge {{ $docente->estado === 'ACTIVO' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $docente->estado }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.docentes.asignar', $docente->id_docente) }}" class="btn btn-info btn-sm">Asignar Grupos</a>
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
