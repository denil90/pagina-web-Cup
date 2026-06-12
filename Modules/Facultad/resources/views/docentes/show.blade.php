@extends('layouts.app')
@section('title', 'Detalle de Postulante Docente')
@section('header', 'Detalle de Postulante')
@section('header-actions')
    <a href="{{ route('admin.docentes.index') }}" class="btn btn-secondary btn-sm">Volver a la Lista</a>
@endsection

@section('content')
<div class="row" style="display: flex; gap: 24px; flex-wrap: wrap;">
    {{-- Personal Info Card --}}
    <div style="flex: 1; min-width: 300px;">
        <div class="card mb-4">
            <div class="card-header">
                <h2>Información Personal</h2>
            </div>
            <div class="card-body">
                <table class="table-info-custom" style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid var(--border-subtle);">
                        <td style="padding: 10px 0; font-weight: 600; color: var(--text-secondary);">Nombre Completo:</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $docente->usuario->nombreCompleto }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-subtle);">
                        <td style="padding: 10px 0; font-weight: 600; color: var(--text-secondary);">CI:</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $docente->usuario->ci }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-subtle);">
                        <td style="padding: 10px 0; font-weight: 600; color: var(--text-secondary);">Correo:</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $docente->usuario->correo }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-subtle);">
                        <td style="padding: 10px 0; font-weight: 600; color: var(--text-secondary);">Teléfono:</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $docente->usuario->telefono ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-subtle);">
                        <td style="padding: 10px 0; font-weight: 600; color: var(--text-secondary);">Dirección:</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $docente->usuario->direccion }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-subtle);">
                        <td style="padding: 10px 0; font-weight: 600; color: var(--text-secondary);">Sexo / Fecha Nac.:</td>
                        <td style="padding: 10px 0; text-align: right;">
                            {{ $docente->usuario->sexo === 'M' ? 'Masculino' : 'Femenino' }} / 
                            {{ \Carbon\Carbon::parse($docente->usuario->fechanac)->format('d/m/Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; font-weight: 600; color: var(--text-secondary);">Estado Postulación:</td>
                        <td style="padding: 10px 0; text-align: right;">
                            @if($docente->estado === 'ACTIVO')
                                <span class="badge badge-success">ACTIVO</span>
                            @elseif($docente->estado === 'PENDIENTE')
                                <span class="badge badge-warning">PENDIENTE</span>
                            @elseif($docente->estado === 'RECHAZADO')
                                <span class="badge badge-danger">RECHAZADO</span>
                            @else
                                <span class="badge badge-secondary">{{ $docente->estado }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Professional info & requirements Card --}}
    <div style="flex: 1.2; min-width: 350px;">
        <div class="card mb-4">
            <div class="card-header">
                <h2>Información Profesional y Documentos</h2>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 20px;">
                    <h3 style="font-size: 1rem; color: var(--accent); margin-bottom: 8px;">Datos Académicos Declarados</h3>
                    <p style="margin-bottom: 6px;"><strong>Título Profesional:</strong> {{ $docente->titulo_profesional }}</p>
                    <p style="margin-bottom: 6px;"><strong>Maestría:</strong> {{ $docente->maestria ?? 'Ninguna declarada' }}</p>
                    <p style="margin-bottom: 6px;"><strong>Diplomado:</strong> {{ $docente->diplomado ?? 'Ninguno declarado' }}</p>
                </div>

                <div style="margin-top: 24px;">
                    <h3 style="font-size: 1rem; color: var(--accent); margin-bottom: 12px;">Archivos Adjuntos (PDF)</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: var(--bg-secondary); border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                            <div>
                                <strong>Título Profesional:</strong>
                            </div>
                            <div>
                                @if($docente->archivo_titulo)
                                    <a href="{{ asset('storage/' . $docente->archivo_titulo) }}" target="_blank" class="btn btn-info btn-sm">📄 Ver Documento</a>
                                @else
                                    <span class="badge badge-danger">No cargado</span>
                                @endif
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: var(--bg-secondary); border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                            <div>
                                <strong>Certificado de Maestría:</strong>
                            </div>
                            <div>
                                @if($docente->archivo_maestria)
                                    <a href="{{ asset('storage/' . $docente->archivo_maestria) }}" target="_blank" class="btn btn-info btn-sm">📄 Ver Documento</a>
                                @else
                                    <span class="badge badge-secondary">No cargado</span>
                                @endif
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: var(--bg-secondary); border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                            <div>
                                <strong>Certificado de Diplomado:</strong>
                            </div>
                            <div>
                                @if($docente->archivo_diplomado)
                                    <a href="{{ asset('storage/' . $docente->archivo_diplomado) }}" target="_blank" class="btn btn-info btn-sm">📄 Ver Documento</a>
                                @else
                                    <span class="badge badge-secondary">No cargado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($docente->estado === 'PENDIENTE')
                    <div style="margin-top: 32px; border-top: 1px solid var(--border-subtle); padding-top: 20px;">
                        <h3 style="font-size: 1rem; margin-bottom: 12px;">Acciones de Verificación</h3>
                        <div class="btn-group">
                            <form action="{{ route('admin.docentes.aprobar', $docente->id_docente) }}" method="POST" onsubmit="return confirm('¿Está seguro de aprobar a este docente?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">Aprobar Postulación</button>
                            </form>
                            
                            <form action="{{ route('admin.docentes.rechazar', $docente->id_docente) }}" method="POST" onsubmit="return confirm('¿Está seguro de rechazar esta postulación?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger">Rechazar Postulación</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
