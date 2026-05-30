@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard Administrativo')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"></div>
        <div>
            <div class="stat-value">{{ $estadisticas['total_postulantes'] }}</div>
            <div class="stat-label">Postulantes {{ $gestionActual?->nombreCompleto ?? '' }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"></div>
        <div>
            <div class="stat-value">{{ $estadisticas['total_grupos'] }}</div>
            <div class="stat-label">Grupos Activos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"></div>
        <div>
            <div class="stat-value">{{ $estadisticas['total_docentes'] }}</div>
            <div class="stat-label">Docentes Activos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"></div>
        <div>
            <div class="stat-value">{{ $estadisticas['total_admitidos'] }}</div>
            <div class="stat-label">Admitidos {{ $gestionActual?->nombreCompleto ?? '' }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Gestiones Registradas</h2>
        <a href="{{ route('admin.gestiones.create') }}" class="btn btn-primary btn-sm">+ Nueva Gestión</a>
    </div>
    <div class="card-body">
        @if($gestiones->isEmpty())
            <div class="empty-state">
                <div class="icon"></div>
                <h3>No hay gestiones registradas</h3>
                <p>Cree una gestión para comenzar a trabajar.</p>
            </div>
        @else
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Gestión</th>
                            <th>Año</th>
                            <th>Semestre</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gestiones as $gestion)
                            <tr>
                                <td><span class="badge badge-primary">{{ $gestion->nombreCompleto }}</span></td>
                                <td>{{ $gestion->anio }}</td>
                                <td>{{ $gestion->semestre }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
