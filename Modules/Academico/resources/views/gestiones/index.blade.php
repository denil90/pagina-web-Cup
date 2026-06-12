@extends('layouts.app')
@section('title', 'Gestiones')
@section('header', 'Gestiones Académicas')
@section('header-actions')
    <a href="{{ route('admin.gestiones.create') }}" class="btn btn-primary btn-sm">+ Nueva Gestión</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if($gestiones->isEmpty())
            <div class="empty-state">
                <div class="icon">📅</div>
                <h3>No hay gestiones registradas</h3>
            </div>
        @else
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Semestre</th>
                            <th>Año</th>
                            <th>Nombre Completo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gestiones as $gestion)
                            <tr>
                                <td>{{ $gestion->id_gestion }}</td>
                                <td>{{ $gestion->semestre }}</td>
                                <td>{{ $gestion->anio }}</td>
                                <td><span class="badge badge-primary">{{ $gestion->nombreCompleto }}</span></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.gestiones.edit', $gestion->id_gestion) }}" class="btn btn-secondary btn-sm">Editar</a>
                                        <form action="{{ route('admin.gestiones.destroy', $gestion->id_gestion) }}" method="POST" onsubmit="return confirm('¿Eliminar esta gestión?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    </div>
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
