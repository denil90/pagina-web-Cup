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
                <thead><tr><th>Nombre</th><th>Edificio</th><th>Capacidad</th><th>Acciones</th></tr></thead>
                <tbody>
                    @foreach($aulas as $a)
                        {{-- Fila de visualización --}}
                        <tr id="aula-view-{{ $a->id_aula }}">
                            <td>{{ $a->nombre }}</td><td>{{ $a->edificio }}</td><td>{{ $a->capacidad }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="toggleEditAula({{ $a->id_aula }})">Editar</button>
                                <form action="{{ route('admin.aulas.destroy', $a->id_aula) }}" method="POST" onsubmit="return confirm('¿Eliminar?')" style="display:inline;">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Eliminar</button></form>
                            </td>
                        </tr>
                        {{-- Fila de edición (oculta por defecto) --}}
                        <tr id="aula-edit-{{ $a->id_aula }}" style="display:none;">
                            <form action="{{ route('admin.aulas.update', $a->id_aula) }}" method="POST">
                                @csrf @method('PUT')
                                <td><input type="text" name="nombre" class="form-control" value="{{ $a->nombre }}" required></td>
                                <td><input type="text" name="edificio" class="form-control" value="{{ $a->edificio }}" required></td>
                                <td><input type="number" name="capacidad" class="form-control" value="{{ $a->capacidad }}" min="1" required></td>
                                <td>
                                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEditAula({{ $a->id_aula }})">Cancelar</button>
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
function toggleEditAula(id) {
    const viewRow = document.getElementById('aula-view-' + id);
    const editRow = document.getElementById('aula-edit-' + id);
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
