@extends('layouts.app')
@section('title', 'Notas')
@section('header', 'Registro de Notas')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2 align-center" style="flex-wrap:wrap;">
            <select name="id_grupo" class="form-control" style="max-width:250px;" onchange="this.form.submit()">
                <option value="">Seleccione un grupo...</option>
                @foreach($grupos as $g)
                    <option value="{{ $g->id_grupo }}" {{ request('id_grupo') == $g->id_grupo ? 'selected' : '' }}>{{ $g->nombre }} — {{ $g->turno?->nombre ?? '' }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

@if($postulantes->isNotEmpty())
<div class="card">
    <div class="card-header"><h2>Estudiantes del Grupo</h2></div>
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        @foreach($materias as $m)
                            <th>{{ $m->nombre }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($postulantes as $p)
                        <tr>
                            <td><strong>{{ $p->usuario->nombreCompleto }}</strong></td>
                            @foreach($materias as $m)
                                @php $nota = $p->notas->where('id_materia', $m->id_materia)->first(); @endphp
                                <td>
                                    @if($nota)
                                        <span class="badge {{ $nota->estado === 'APROBADO' ? 'badge-success' : 'badge-danger' }}">{{ $nota->promedio }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                    <a href="{{ route('admin.notas.registrar', [$p->id_postulante, $m->id_materia]) }}" class="btn btn-secondary btn-sm" style="padding:3px 8px;font-size:0.7rem;">✏️</a>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
