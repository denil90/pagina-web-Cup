@extends('layouts.app')
@section('title', 'Postulantes')
@section('header', 'Postulantes del CUP')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2 align-center" style="flex-wrap: wrap;">
            <select name="id_gestion" class="form-control" style="max-width: 200px;">
                <option value="">Todas las gestiones</option>
                @foreach($gestiones as $g)
                    <option value="{{ $g->id_gestion }}" {{ request('id_gestion') == $g->id_gestion ? 'selected' : '' }}>{{ $g->nombreCompleto }}</option>
                @endforeach
            </select>
            <input type="text" name="buscar" class="form-control" style="max-width: 250px;" placeholder="Buscar por nombre o CI..." value="{{ request('buscar') }}">
            <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Nombre</th><th>CI</th><th>Carrera 1ª</th><th>Requisitos</th><th>Pago</th><th>Grupo</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse($postulantes as $p)
                        <tr>
                            <td><strong>{{ $p->usuario->nombreCompleto }}</strong></td>
                            <td>{{ $p->usuario->ci }}</td>
                            <td>{{ $p->carreraPrimera?->nombre ?? '—' }}</td>
                            <td>
                                @if($p->cumpleRequisitos())
                                    <span class="badge badge-success">Completos</span>
                                @else
                                    <span class="badge badge-warning">Pendientes</span>
                                @endif
                            </td>
                            <td>
                                @if($p->tienePagoConfirmado())
                                    <span class="badge badge-success">Pagado</span>
                                @else
                                    <span class="badge badge-danger">Pendiente</span>
                                @endif
                            </td>
                            <td>{{ $p->grupo?->nombre ?? 'Sin asignar' }}</td>
                            <td>
                                <a href="{{ route('admin.postulantes.show', $p->id_postulante) }}" class="btn btn-info btn-sm">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No hay postulantes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2">{{ $postulantes->appends(request()->query())->links() }}</div>
    </div>
</div>
@endsection
