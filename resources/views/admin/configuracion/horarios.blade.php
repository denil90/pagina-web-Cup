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
                <thead><tr><th>Día</th><th>Hora Inicio</th><th>Hora Final</th><th>Acciones</th></tr></thead>
                <tbody>
                    @foreach($horarios as $h)
                        {{-- Fila de visualización --}}
                        <tr id="horario-view-{{ $h->id_horario }}">
                            <td>{{ $h->dia }}</td><td>{{ $h->hora_inicio }}</td><td>{{ $h->hora_final }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="toggleEditHorario({{ $h->id_horario }})">Editar</button>
                                <form action="{{ route('admin.horarios.destroy', $h->id_horario) }}" method="POST" onsubmit="return confirm('¿Eliminar?')" style="display:inline;">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Eliminar</button></form>
                            </td>
                        </tr>
                        {{-- Fila de edición (oculta por defecto) --}}
                        <tr id="horario-edit-{{ $h->id_horario }}" style="display:none;">
                            <form action="{{ route('admin.horarios.update', $h->id_horario) }}" method="POST">
                                @csrf @method('PUT')
                                <td>
                                    <select name="dia" class="form-control" required>
                                        @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] as $d)
                                            <option {{ $h->dia === $d ? 'selected' : '' }}>{{ $d }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="time" name="hora_inicio" class="form-control" value="{{ \Illuminate\Support\Str::substr($h->hora_inicio, 0, 5) }}" required></td>
                                <td><input type="time" name="hora_final" class="form-control" value="{{ \Illuminate\Support\Str::substr($h->hora_final, 0, 5) }}" required></td>
                                <td>
                                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEditHorario({{ $h->id_horario }})">Cancelar</button>
                                </td>
                            </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleEditHorario(id) {
    const viewRow = document.getElementById('horario-view-' + id);
    const editRow = document.getElementById('horario-edit-' + id);
    if (editRow.style.display === 'none') {
        viewRow.style.display = 'none';
        editRow.style.display = '';
    } else {
        viewRow.style.display = '';
        editRow.style.display = 'none';
    }
}
</script>
@endsection
