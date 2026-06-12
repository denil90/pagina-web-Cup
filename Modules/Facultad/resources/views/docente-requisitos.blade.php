@extends('layouts.app')
@section('title', 'Documentos del Docente')
@section('header', 'Gestión de Documentos')

@section('content')
<div class="fade-in">
    <a href="{{ route('docente.dashboard') }}" class="back-to-portal mb-3" style="display: inline-flex;">← Volver al Dashboard</a>

    <div class="alert alert-info" style="font-size: 0.88rem;">
        📋 Ingrese los nombres de sus grados académicos y suba los documentos correspondientes en formato PDF (máx. 5MB). Un administrador revisará su postulación.
    </div>

    <div class="requisitos-grid">
        {{-- Título Profesional --}}
        <div class="requisito-card {{ $docente->archivo_titulo ? 'uploaded' : '' }}">
            <div class="requisito-icon">
                @if($docente->archivo_titulo)
                    <div class="requisito-check">✓</div>
                @else
                    🎓
                @endif
            </div>
            <div class="requisito-title">Título Profesional</div>
            <div class="requisito-status {{ $docente->archivo_titulo ? 'status-uploaded' : 'status-pending' }}">
                {{ $docente->archivo_titulo ? '✓ Documento subido' : '⏳ Pendiente' }}
            </div>

            @if($docente->archivo_titulo)
                <a href="{{ asset('storage/' . $docente->archivo_titulo) }}" target="_blank" class="btn btn-info btn-sm mb-3">
                    Ver PDF Subido
                </a>
            @endif

            <form method="POST" action="{{ route('docente.requisitos.titulo') }}" enctype="multipart/form-data" class="requisito-form" style="text-align: left;">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label" style="font-weight: 500;">Título Profesional *</label>
                    <input type="text" name="titulo_profesional" class="form-control" value="{{ old('titulo_profesional', $docente->titulo_profesional) }}" placeholder="Ej: Licenciatura en Ingeniería de Sistemas" required>
                </div>
                <div class="file-upload-area mb-3 {{ $docente->archivo_titulo ? 'has-file' : '' }}">
                    <input type="file" name="archivo_titulo" class="file-input" accept=".pdf" {{ $docente->archivo_titulo ? '' : 'required' }}>
                    <label class="file-label">
                        <span class="file-icon">📄</span>
                        <span class="file-text">{{ $docente->archivo_titulo ? 'Reemplazar PDF' : 'Seleccionar PDF' }}</span>
                        <span class="file-hint">PDF, máximo 5MB</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Guardar Título</button>
            </form>
        </div>

        {{-- Maestría --}}
        <div class="requisito-card {{ $docente->archivo_maestria ? 'uploaded' : '' }}">
            <div class="requisito-icon">
                @if($docente->archivo_maestria)
                    <div class="requisito-check">✓</div>
                @else
                    📚
                @endif
            </div>
            <div class="requisito-title">Maestría (Opcional)</div>
            <div class="requisito-status {{ $docente->archivo_maestria ? 'status-uploaded' : 'status-pending' }}">
                {{ $docente->archivo_maestria ? '✓ Documento subido' : '⏳ Opcional' }}
            </div>

            @if($docente->archivo_maestria)
                <a href="{{ asset('storage/' . $docente->archivo_maestria) }}" target="_blank" class="btn btn-info btn-sm mb-3">
                    Ver PDF Subido
                </a>
            @endif

            <form method="POST" action="{{ route('docente.requisitos.maestria') }}" enctype="multipart/form-data" class="requisito-form" style="text-align: left;">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label" style="font-weight: 500;">Nombre de la Maestría</label>
                    <input type="text" name="maestria" class="form-control" value="{{ old('maestria', $docente->maestria) }}" placeholder="Ej: Maestría en Ciencias de la Computación">
                </div>
                <div class="file-upload-area mb-3 {{ $docente->archivo_maestria ? 'has-file' : '' }}">
                    <input type="file" name="archivo_maestria" class="file-input" accept=".pdf">
                    <label class="file-label">
                        <span class="file-icon">📄</span>
                        <span class="file-text">{{ $docente->archivo_maestria ? 'Reemplazar PDF' : 'Seleccionar PDF' }}</span>
                        <span class="file-hint">PDF, máximo 5MB</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Guardar Maestría</button>
            </form>
        </div>

        {{-- Diplomado --}}
        <div class="requisito-card {{ $docente->archivo_diplomado ? 'uploaded' : '' }}">
            <div class="requisito-icon">
                @if($docente->archivo_diplomado)
                    <div class="requisito-check">✓</div>
                @else
                    📜
                @endif
            </div>
            <div class="requisito-title">Diplomado (Opcional)</div>
            <div class="requisito-status {{ $docente->archivo_diplomado ? 'status-uploaded' : 'status-pending' }}">
                {{ $docente->archivo_diplomado ? '✓ Documento subido' : '⏳ Opcional' }}
            </div>

            @if($docente->archivo_diplomado)
                <a href="{{ asset('storage/' . $docente->archivo_diplomado) }}" target="_blank" class="btn btn-info btn-sm mb-3">
                    Ver PDF Subido
                </a>
            @endif

            <form method="POST" action="{{ route('docente.requisitos.diplomado') }}" enctype="multipart/form-data" class="requisito-form" style="text-align: left;">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label" style="font-weight: 500;">Nombre del Diplomado</label>
                    <input type="text" name="diplomado" class="form-control" value="{{ old('diplomado', $docente->diplomado) }}" placeholder="Ej: Diplomado en Educación Superior">
                </div>
                <div class="file-upload-area mb-3 {{ $docente->archivo_diplomado ? 'has-file' : '' }}">
                    <input type="file" name="archivo_diplomado" class="file-input" accept=".pdf">
                    <label class="file-label">
                        <span class="file-icon">📄</span>
                        <span class="file-text">{{ $docente->archivo_diplomado ? 'Reemplazar PDF' : 'Seleccionar PDF' }}</span>
                        <span class="file-hint">PDF, máximo 5MB</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Guardar Diplomado</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.file-input').forEach(input => {
            input.addEventListener('change', function(e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : '';
                const area = this.closest('.file-upload-area');
                const labelText = area.querySelector('.file-text');
                if (fileName) {
                    labelText.textContent = fileName;
                    area.classList.add('has-file');
                } else {
                    labelText.textContent = 'Seleccionar PDF';
                    area.classList.remove('has-file');
                }
            });
        });
    });
</script>
@endpush
@endsection
