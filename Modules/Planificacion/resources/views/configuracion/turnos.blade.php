@extends('layouts.app')
@section('title', 'Turnos')
@section('header', 'Gestión de Turnos')

@section('content')
<div class="card mb-3">
    <div class="card-header"><h2>Agregar Turno</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.turnos.store') }}" class="d-flex gap-2 align-center">
            @csrf
            <div class="form-group" style="flex:1;"><input type="text" name="nombre" class="form-control" placeholder="Ej: Mañana, Tarde, Noche" required></div>
            <button class="btn btn-primary">Agregar</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead><tr><th>Turno</th><th>Acción</th></tr></thead>
                <tbody>
                    @foreach($turnos as $t)
                        <tr>
                            <td><span class="badge badge-info">{{ $t->nombre }}</span></td>
                            <td><form action="{{ route('admin.turnos.destroy', $t->id_turno) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Eliminar</button></form></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
