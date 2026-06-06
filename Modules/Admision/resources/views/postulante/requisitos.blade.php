@extends('layouts.app')
@section('title', 'Mis Requisitos')
@section('header', 'Subir Requisitos')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <h2>Documentos Requeridos</h2>
    </div>
    <div class="card-body">
        <p class="text-muted" style="margin-bottom: 20px;">
            Para completar tu inscripción debes subir los siguientes documentos en formato <strong>PDF</strong> (máximo 5 MB cada uno).
            El administrador revisará tus documentos y los aprobará.
        </p>

        <div class="requisitos-grid">
            {{-- Título de Bachiller --}}
            <div class="requisito-card {{ $postulante->archivo_titulo_bachiller ? 'uploaded' : '' }}">
                <div class="requisito-icon">
                    @if($postulante->archivo_titulo_bachiller)
                        <span class="requisito-check">✓</span>
                    @else
                        <span></span>
                    @endif
                </div>
                <h3 class="requisito-title">Título de Bachiller</h3>

                @if($postulante->archivo_titulo_bachiller)
                    <div class="requisito-status status-uploaded">
                        <span>Documento subido</span>
                    </div>
                    <div class="requisito-actions">
                        <a href="{{ asset('storage/' . $postulante->archivo_titulo_bachiller) }}" target="_blank" class="btn btn-info btn-sm">
                            Ver PDF
                        </a>
                    </div>
                    <p class="requisito-hint mt-1">¿Subiste el archivo incorrecto? Puedes reemplazarlo:</p>
                @else
                    <div class="requisito-status status-pending">
                        <span>Pendiente de subir</span>
                    </div>
                @endif

                <form action="{{ route('postulante.requisitos.titulo') }}" method="POST" enctype="multipart/form-data" class="requisito-form">
                    @csrf
                    <div class="file-upload-area" id="dropzone-titulo">
                        <input type="file" name="archivo_titulo_bachiller" id="file-titulo" accept=".pdf" required class="file-input">
                        <label for="file-titulo" class="file-label">
                            <span class="file-icon"></span>
                            <span class="file-text" id="file-text-titulo">Haz clic para seleccionar tu PDF</span>
                            <span class="file-hint">Solo archivos PDF — Máx. 5 MB</span>
                        </label>
                    </div>
                    @error('archivo_titulo_bachiller')
                        <p class="text-danger" style="font-size:0.82rem; margin-top:6px;">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="btn btn-primary w-100 mt-2">Subir Título de Bachiller</button>
                </form>

                @if($postulante->titulo_bachiller)
                    <div class="requisito-verified mt-2">
                        <span>Verificado por el administrador</span>
                    </div>
                @endif
            </div>

            {{-- Libreta de Último Año --}}
            <div class="requisito-card {{ $postulante->archivo_libreta ? 'uploaded' : '' }}">
                <div class="requisito-icon">
                    @if($postulante->archivo_libreta)
                        <span class="requisito-check">✓</span>
                    @else
                        <span></span>
                    @endif
                </div>
                <h3 class="requisito-title">Libreta de Último Año</h3>

                @if($postulante->archivo_libreta)
                    <div class="requisito-status status-uploaded">
                        <span>Documento subido</span>
                    </div>
                    <div class="requisito-actions">
                        <a href="{{ asset('storage/' . $postulante->archivo_libreta) }}" target="_blank" class="btn btn-info btn-sm">
                            Ver PDF
                        </a>
                    </div>
                    <p class="requisito-hint mt-1">¿Subiste el archivo incorrecto? Puedes reemplazarlo:</p>
                @else
                    <div class="requisito-status status-pending">
                        <span>Pendiente de subir</span>
                    </div>
                @endif

                <form action="{{ route('postulante.requisitos.libreta') }}" method="POST" enctype="multipart/form-data" class="requisito-form">
                    @csrf
                    <div class="file-upload-area" id="dropzone-libreta">
                        <input type="file" name="archivo_libreta" id="file-libreta" accept=".pdf" required class="file-input">
                        <label for="file-libreta" class="file-label">
                            <span class="file-icon"></span>
                            <span class="file-text" id="file-text-libreta">Haz clic para seleccionar tu PDF</span>
                            <span class="file-hint">Solo archivos PDF — Máx. 5 MB</span>
                        </label>
                    </div>
                    @error('archivo_libreta')
                        <p class="text-danger" style="font-size:0.82rem; margin-top:6px;">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="btn btn-primary w-100 mt-2">Subir Libreta de Último Año</button>
                </form>

                @if($postulante->libreta_de_ultimo_anio)
                    <div class="requisito-verified mt-2">
                        <span>Verificado por el administrador</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mostrar nombre del archivo seleccionado
    document.querySelectorAll('.file-input').forEach(input => {
        input.addEventListener('change', function() {
            const textEl = this.closest('.file-upload-area').querySelector('.file-text');
            if (this.files.length > 0) {
                const file = this.files[0];
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                textEl.textContent = `${file.name} (${sizeMB} MB)`;
                this.closest('.file-upload-area').classList.add('has-file');
            } else {
                textEl.textContent = 'Haz clic para seleccionar tu PDF';
                this.closest('.file-upload-area').classList.remove('has-file');
            }
        });
    });
</script>
@endpush
@endsection
