@extends('layouts.app')
@section('title', 'Grupo ' . $estadisticas['grupo']->nombre)
@section('header', 'Grupo: ' . $estadisticas['grupo']->nombre)

@section('content')
<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon"></div><div><div class="stat-value">{{ $estadisticas['total_inscritos'] }}</div><div class="stat-label">Inscritos</div></div></div>
    <div class="stat-card"><div class="stat-icon"></div><div><div class="stat-value">{{ $estadisticas['aprobados'] }}</div><div class="stat-label">Aprobados</div></div></div>
    <div class="stat-card"><div class="stat-icon"></div><div><div class="stat-value">{{ $estadisticas['reprobados'] }}</div><div class="stat-label">Reprobados</div></div></div>
    <div class="stat-card"><div class="stat-icon"></div><div><div class="stat-value">{{ $estadisticas['porcentaje_aprobacion'] }}%</div><div class="stat-label">% Aprobación</div></div></div>
</div>

<div class="card">
    <div class="card-header"><h2>Docentes Asignados</h2></div>
    <div class="card-body">
        @foreach($estadisticas['grupo']->docenteGrupos as $dg)
            <span class="badge badge-primary" style="margin: 4px;">{{ $dg->docente->usuario->nombreCompleto }} — {{ $dg->materia->nombre }}</span>
        @endforeach
    </div>
</div>
@endsection
