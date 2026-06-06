@extends('layouts.app')
@section('title', $titulo)
@section('header', $titulo)
@section('header-actions')
    <div class="btn-group">
        @if(isset($gestion))
            <form method="POST" action="{{ route('admin.reportes.exportar.csv') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="tipo" value="{{ $tipo }}">
                <input type="hidden" name="id_gestion" value="{{ $gestion->id_gestion }}">
                <button class="btn btn-success btn-sm">📥 CSV</button>
            </form>
        @endif
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-secondary btn-sm">← Volver</a>
    </div>
@endsection

@section('content')
@if($tipo === 'aprobados_gestion')
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead><tr><th>#</th><th>Nombre</th><th>CI</th><th>Carrera</th><th>Nota</th><th>Opción</th></tr></thead>
                    <tbody>
                        @foreach($datos as $i => $a)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $a->postulante->usuario->nombreCompleto }}</td>
                                <td>{{ $a->postulante->usuario->ci }}</td>
                                <td>{{ $a->carrera->nombre }}</td>
                                <td><strong>{{ $a->nota_final_cup }}</strong></td>
                                <td><span class="badge {{ $a->opcion_ingreso === 'PRIMERA OPCION' ? 'badge-success' : 'badge-warning' }}">{{ $a->opcion_ingreso }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@elseif($tipo === 'rendimiento_grupo')
    <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon">👥</div><div><div class="stat-value">{{ $datos['total'] }}</div><div class="stat-label">Total</div></div></div>
        <div class="stat-card"><div class="stat-icon">✅</div><div><div class="stat-value">{{ $datos['aprobados'] }}</div><div class="stat-label">Aprobados</div></div></div>
        <div class="stat-card"><div class="stat-icon">❌</div><div><div class="stat-value">{{ $datos['reprobados'] }}</div><div class="stat-label">Reprobados</div></div></div>
        <div class="stat-card"><div class="stat-icon">📊</div><div><div class="stat-value">{{ $datos['porcentaje_aprobacion'] }}%</div><div class="stat-label">% Aprobación</div></div></div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead><tr><th>Estudiante</th><th>Promedio</th><th>Estado</th></tr></thead>
                    <tbody>
                        @foreach($datos['postulantes'] as $p)
                            <tr>
                                <td>{{ $p->usuario->nombreCompleto }}</td>
                                <td><strong>{{ $p->promedioGeneral() }}</strong></td>
                                <td><span class="badge {{ $p->aproboTodasLasMaterias() ? 'badge-success' : 'badge-danger' }}">{{ $p->aproboTodasLasMaterias() ? 'APROBADO' : 'REPROBADO' }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@elseif($tipo === 'docente_destacado')
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead><tr><th>#</th><th>Docente</th><th>Estudiantes</th><th>Aprobados</th><th>% Aprobación</th></tr></thead>
                    <tbody>
                        @foreach($datos as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $d['docente']->usuario->nombreCompleto }}</strong></td>
                                <td>{{ $d['total_estudiantes'] }}</td>
                                <td>{{ $d['aprobados'] }}</td>
                                <td><span class="badge {{ $d['porcentaje'] >= 70 ? 'badge-success' : ($d['porcentaje'] >= 50 ? 'badge-warning' : 'badge-danger') }}">{{ $d['porcentaje'] }}%</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@elseif($tipo === 'comparativa')
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead><tr><th>Gestión</th><th>Postulantes</th><th>Admitidos</th><th>No Admitidos</th><th>Tasa Admisión</th></tr></thead>
                    <tbody>
                        @foreach($datos as $d)
                            <tr>
                                <td><strong>{{ $d['gestion'] }}</strong></td>
                                <td>{{ $d['postulantes'] }}</td>
                                <td><span class="badge badge-success">{{ $d['admitidos'] }}</span></td>
                                <td><span class="badge badge-danger">{{ $d['no_admitidos'] }}</span></td>
                                <td><strong>{{ $d['tasa_admision'] }}%</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@elseif($tipo === 'por_carrera')
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead><tr><th>Carrera</th><th>Cupo Máximo</th><th>Admitidos</th><th>1ª Opción</th><th>2ª Opción</th></tr></thead>
                    <tbody>
                        @foreach($datos as $d)
                            <tr>
                                <td><strong>{{ $d['carrera'] }}</strong></td>
                                <td>{{ $d['cupo_maximo'] }}</td>
                                <td><span class="badge badge-primary">{{ $d['admitidos'] }}</span></td>
                                <td>{{ $d['primera_opcion'] }}</td>
                                <td>{{ $d['segunda_opcion'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
