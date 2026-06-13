@extends('layouts.app')
@section('title', 'Carreras')
@section('header', 'Carreras - FIC')
@section('header-actions')
    <a href="{{ route('admin.carreras.create') }}" class="btn btn-primary btn-sm">+ Nueva Carrera</a>
@endsection

@section('content')
@if(isset($gestiones) && count($gestiones) > 0)
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form method="GET" action="{{ route('admin.carreras.index') }}" id="filterForm">
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <label for="id_gestion" style="font-size: 0.88rem; font-weight: 500; color: var(--text-secondary);">Filtrar Admitidos por Gestión:</label>
                    <select name="id_gestion" id="id_gestion" class="form-control" style="width: auto; min-width: 220px; padding: 6px 36px 6px 12px;" onchange="this.form.submit()">
                        @foreach($gestiones as $g)
                            <option value="{{ $g->id_gestion }}" {{ (isset($id_gestion) && $id_gestion == $g->id_gestion) ? 'selected' : '' }}>
                                Gestión {{ $g->semestre }} - {{ $g->anio }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Carrera</th>
                        <th>Descripción</th>
                        <th>Cupo Máximo</th>
                        <th>Admitidos</th>
                        <th>Disponibles</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($carreras as $carrera)
                        @php
                            $admitidos = $carrera->admitidosCount($id_gestion);
                            $disponibles = $carrera->cupo_maximo - $admitidos;
                        @endphp
                        <tr>
                            <td><strong>{{ $carrera->nombre }}</strong></td>
                            <td>{{ Str::limit($carrera->descripcion, 50) }}</td>
                            <td><span class="badge badge-info">{{ $carrera->cupo_maximo }}</span></td>
                            <td>{{ $admitidos }}</td>
                            <td><span class="badge badge-{{ $disponibles <= 0 ? 'danger' : 'success' }}">{{ $disponibles }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.carreras.edit', $carrera->id) }}" class="btn btn-secondary btn-sm">Editar</a>
                                    <form action="{{ route('admin.carreras.destroy', $carrera->id) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
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
    </div>
</div>
@endsection
