@extends('layouts.app')
@section('title', 'Horarios')
@section('header', 'Gestión de Horarios')

@section('content')
<div class="card mb-3">
    <div class="card-header"><h2>Agregar Horario</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.horarios.store') }}" class="form-row">
            @csrf
            <div class="form-group"><label class="form-label">Día</label>
                <select name="dia" class="form-control" required>
                    @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] as $d)
                        <option>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label class="form-label">Hora Inicio</label><input type="time" name="hora_inicio" class="form-control" required></div>
            <div class="form-group"><label class="form-label">Hora Final</label><input type="time" name="hora_final" class="form-control" required></div>
            <div class="form-group" style="align-self:end;"><button class="btn btn-primary">Agregar</button></div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead><tr><th>Día</th><th>Hora Inicio</th><th>Hora Final</th><th>Acción</th></tr></thead>
                <tbody>
                    @foreach($horarios as $h)
                        <tr>
                            <td>{{ $h->dia }}</td><td>{{ $h->hora_inicio }}</td><td>{{ $h->hora_final }}</td>
                            <td><form action="{{ route('admin.horarios.destroy', $h->id_horario) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Eliminar</button></form></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
