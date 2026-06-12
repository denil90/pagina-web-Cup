@extends('layouts.app')
@section('title', 'Carreras')
@section('header', 'Carreras - FIC')
@section('header-actions')
    <a href="{{ route('admin.carreras.create') }}" class="btn btn-primary btn-sm">+ Nueva Carrera</a>
@endsection

@section('content')
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
                        <tr>
                            <td><strong>{{ $carrera->nombre }}</strong></td>
                            <td>{{ Str::limit($carrera->descripcion, 50) }}</td>
                            <td><span class="badge badge-info">{{ $carrera->cupo_maximo }}</span></td>
                            <td>{{ $carrera->admitidos_count }}</td>
                            <td><span class="badge badge-success">{{ $carrera->cupo_maximo - $carrera->admitidos_count }}</span></td>
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
