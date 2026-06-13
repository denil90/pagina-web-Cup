@extends('layouts.app')
@section('title', 'Bitácora del Sistema')
@section('header', 'Bitácora de Actividades')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bitacora.index') }}" class="d-flex gap-2 align-items-end" style="flex-wrap: wrap;">
            <div class="form-group mb-0" style="flex: 1; min-width: 200px;">
                <label class="form-label">Filtrar por Módulo</label>
                <select name="modulo" class="form-control">
                    <option value="">Todos los módulos</option>
                    @foreach($modulos as $m)
                        <option value="{{ $m }}" {{ request('modulo') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0" style="flex: 1; min-width: 200px;">
                <label class="form-label">Filtrar por Acción</label>
                <select name="accion" class="form-control">
                    <option value="">Todas las acciones</option>
                    @foreach($acciones as $a)
                        <option value="{{ $a }}" {{ request('accion') == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary">Filtrar</button>
            <a href="{{ route('admin.bitacora.index') }}" class="btn btn-secondary">Limpiar</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>IP</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="white-space: nowrap;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td><code>{{ $log->ip_address ?? 'Desconocida' }}</code></td>
                            <td>
                                @if($log->usuario)
                                    <strong>{{ $log->usuario->nombreCompleto }}</strong><br>
                                    <small class="text-muted">{{ $log->usuario->rol }}</small>
                                @else
                                    <span class="text-muted">Sistema / No Autenticado</span>
                                @endif
                            </td>
                            <td><span class="badge badge-secondary">{{ $log->modulo }}</span></td>
                            <td><span class="badge badge-primary">{{ $log->accion }}</span></td>
                            <td>{{ $log->descripcion }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No hay registros en la bitácora.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
