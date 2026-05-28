@extends('layouts.app')
@section('title', 'Aulas')
@section('header', 'Gestión de Aulas')

@section('content')
<div class="card mb-3">
    <div class="card-header"><h2>Agregar Aula</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.aulas.store') }}" class="form-row">
            @csrf
            <div class="form-group"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" placeholder="Aula 101" required></div>
            <div class="form-group"><label class="form-label">Edificio</label><input type="text" name="edificio" class="form-control" placeholder="Edificio A" required></div>
            <div class="form-group"><label class="form-label">Capacidad</label><input type="number" name="capacidad" class="form-control" value="70" min="1" required></div>
            <div class="form-group" style="align-self:end;"><button class="btn btn-primary">Agregar</button></div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead><tr><th>Nombre</th><th>Edificio</th><th>Capacidad</th><th>Acción</th></tr></thead>
                <tbody>
                    @foreach($aulas as $a)
                        <tr>
                            <td>{{ $a->nombre }}</td><td>{{ $a->edificio }}</td><td>{{ $a->capacidad }}</td>
                            <td><form action="{{ route('admin.aulas.destroy', $a->id_aula) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Eliminar</button></form></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
