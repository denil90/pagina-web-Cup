@extends('layouts.app')
@section('title', 'Materias')
@section('header', 'Materias del CUP')
@section('header-actions')
    <a href="{{ route('admin.materias.create') }}" class="btn btn-primary btn-sm">+ Nueva Materia</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Examen 1 (%)</th>
                        <th>Examen 2 (%)</th>
                        <th>Examen Final (%)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materias as $materia)
                        <tr>
                            <td><strong>{{ $materia->nombre }}</strong></td>
                            <td>{{ $materia->porcentaje_examen1 }}%</td>
                            <td>{{ $materia->porcentaje_examen2 }}%</td>
                            <td>{{ $materia->porcentaje_examen3 }}%</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.materias.edit', $materia->id_materia) }}" class="btn btn-secondary btn-sm">Editar</a>
                                    <form action="{{ route('admin.materias.destroy', $materia->id_materia) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
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
