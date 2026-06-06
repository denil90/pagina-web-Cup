@extends('layouts.app')
@section('title', 'Resultados Admisión')
@section('header', 'Resultados — ' . $gestion->nombreCompleto)

@section('content')
<div class="stats-grid" style="grid-template-columns: repeat(2,1fr);">
    <div class="stat-card"><div class="stat-icon"></div><div><div class="stat-value">{{ $admitidos->count() }}</div><div class="stat-label">Total Admitidos</div></div></div>
    <div class="stat-card"><div class="stat-icon"></div><div><div class="stat-value">{{ $admitidos->where('opcion_ingreso','PRIMERA OPCION')->count() }}</div><div class="stat-label">En Primera Opción</div></div></div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead><tr><th>#</th><th>Nombre</th><th>CI</th><th>Carrera Admitida</th><th>Nota Final</th><th>Opción</th></tr></thead>
                <tbody>
                    @foreach($admitidos as $i => $a)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $a->postulante->usuario->nombreCompleto }}</strong></td>
                            <td>{{ $a->postulante->usuario->ci }}</td>
                            <td>{{ $a->carrera->nombre }}</td>
                            <td><strong>{{ $a->nota_final_cup }}</strong></td>
                            <td><span class="badge {{ $a->fueAdmitidoEnPrimeraOpcion() ? 'badge-success' : 'badge-warning' }}">{{ $a->opcion_ingreso }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
