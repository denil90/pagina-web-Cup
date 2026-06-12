@extends('layouts.app')
@section('title', 'Reportes')
@section('header', 'Centro de Reportes')

@section('content')
<div class="report-grid">
    {{-- Aprobados por Gestión --}}
    <div class="card">
        <div class="card-body">
            <h3 style="color:var(--accent);margin-bottom:12px;">📋 Admitidos por Gestión</h3>
            <p class="text-muted mb-2">Lista de todos los admitidos en una gestión con nota, carrera y opción de ingreso.</p>
            <form method="POST" action="{{ route('admin.reportes.aprobados') }}">
                @csrf
                <select name="id_gestion" class="form-control mb-2" required>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id_gestion }}">{{ $g->nombreCompleto }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm w-100">Generar</button>
            </form>
        </div>
    </div>

    {{-- Rendimiento por Grupo --}}
    <div class="card">
        <div class="card-body">
            <h3 style="color:var(--accent);margin-bottom:12px;">📊 Rendimiento por Grupo</h3>
            <p class="text-muted mb-2">% de aprobación, lista de postulantes con notas por grupo.</p>
            <form method="POST" action="{{ route('admin.reportes.rendimiento') }}">
                @csrf
                <select name="id_grupo" class="form-control mb-2" required>
                    @foreach($grupos as $g)
                        <option value="{{ $g->id_grupo }}">{{ $g->nombre }} — {{ $g->turno?->nombre ?? '' }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm w-100">Generar</button>
            </form>
        </div>
    </div>

    {{-- Ranking Docentes --}}
    <div class="card">
        <div class="card-body">
            <h3 style="color:var(--accent);margin-bottom:12px;">🏆 Ranking de Docentes</h3>
            <p class="text-muted mb-2">Docente con mayor porcentaje de aprobados en sus grupos.</p>
            <form method="POST" action="{{ route('admin.reportes.docente') }}">
                @csrf
                <select name="id_gestion" class="form-control mb-2" required>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id_gestion }}">{{ $g->nombreCompleto }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm w-100">Generar</button>
            </form>
        </div>
    </div>

    {{-- Admitidos por Carrera --}}
    <div class="card">
        <div class="card-body">
            <h3 style="color:var(--accent);margin-bottom:12px;">🎯 Admitidos por Carrera</h3>
            <p class="text-muted mb-2">Cantidad de admitidos vs cupo por carrera.</p>
            <form method="POST" action="{{ route('admin.reportes.carreras') }}">
                @csrf
                <select name="id_gestion" class="form-control mb-2" required>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id_gestion }}">{{ $g->nombreCompleto }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm w-100">Generar</button>
            </form>
        </div>
    </div>

    {{-- Comparativa --}}
    <div class="card" style="grid-column: span 2;">
        <div class="card-body">
            <h3 style="color:var(--accent);margin-bottom:12px;">📈 Comparativa entre Gestiones</h3>
            <p class="text-muted mb-2">Compara postulantes vs admitidos entre diferentes gestiones.</p>
            <form method="POST" action="{{ route('admin.reportes.comparativa') }}">
                @csrf
                <div class="d-flex gap-1" style="flex-wrap:wrap;">
                    @foreach($gestiones as $g)
                        <label class="form-check">
                            <input type="checkbox" name="gestiones[]" value="{{ $g->id_gestion }}">
                            {{ $g->nombreCompleto }}
                        </label>
                    @endforeach
                </div>
                <button class="btn btn-primary btn-sm mt-2">Comparar</button>
            </form>
        </div>
    </div>
</div>
@endsection
