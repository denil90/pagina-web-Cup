@extends('layouts.app')
@section('title', 'Detalle Postulante')
@section('header', 'Postulante: ' . $postulante->usuario->nombreCompleto)

@section('content')
<div class="form-row">
    {{-- Datos personales --}}
    <div class="card">
        <div class="card-header"><h2>Datos Personales</h2></div>
        <div class="card-body">
            <p><strong>CI:</strong> {{ $postulante->usuario->ci }}</p>
            <p><strong>Correo:</strong> {{ $postulante->usuario->correo }}</p>
            <p><strong>Teléfono:</strong> {{ $postulante->usuario->telefono ?? '—' }}</p>
            <p><strong>Colegio:</strong> {{ $postulante->colegio_procedencia }}</p>
            <p><strong>Ciudad:</strong> {{ $postulante->ciudad }}</p>
            <p><strong>Gestión:</strong> {{ $postulante->gestion?->nombreCompleto ?? '—' }}</p>
        </div>
    </div>

    {{-- Carreras --}}
    <div class="card">
        <div class="card-header"><h2>Carreras Seleccionadas</h2></div>
        <div class="card-body">
            <p><strong>1ª Opción:</strong> {{ $postulante->carreraPrimera?->nombre ?? '—' }}</p>
            <p><strong>2ª Opción:</strong> {{ $postulante->carreraSegunda?->nombre ?? '—' }}</p>
            <p><strong>Grupo:</strong> {{ $postulante->grupo?->nombre ?? 'Sin asignar' }}</p>
        </div>
    </div>
</div>

{{-- Requisitos --}}
<div class="card mt-3">
    <div class="card-header"><h2>Verificación de Requisitos</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.postulantes.requisitos', $postulante->id_postulante) }}">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-check">
                        <input type="hidden" name="titulo_bachiller" value="0">
                        <input type="checkbox" name="titulo_bachiller" value="1" {{ $postulante->titulo_bachiller ? 'checked' : '' }}>
                        Título de Bachiller
                    </label>
                </div>
                <div class="form-group">
                    <label class="form-check">
                        <input type="hidden" name="libreta_de_ultimo_anio" value="0">
                        <input type="checkbox" name="libreta_de_ultimo_anio" value="1" {{ $postulante->libreta_de_ultimo_anio ? 'checked' : '' }}>
                        Libreta de Último Año
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Actualizar Requisitos</button>
        </form>
    </div>
</div>

{{-- Asignar grupo --}}
@if(!$postulante->id_grupo && $postulante->cumpleRequisitos() && $postulante->tienePagoConfirmado())
<div class="card mt-3">
    <div class="card-header"><h2>Asignar Grupo</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.postulantes.grupo', $postulante->id_postulante) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <select name="id_grupo" class="form-control" required>
                    <option value="">Seleccione un grupo...</option>
                    @foreach(\App\Models\Grupo::with('turno')->get() as $grupo)
                        @if($grupo->tieneDisponibilidad())
                            <option value="{{ $grupo->id_grupo }}">{{ $grupo->nombre }} — {{ $grupo->turno?->nombre ?? '' }} ({{ $grupo->inscritosActuales() }}/{{ $grupo->capacidad_maxima }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-success btn-sm">Asignar</button>
        </form>
    </div>
</div>
@endif

{{-- Notas --}}
@if($postulante->notas->isNotEmpty())
<div class="card mt-3">
    <div class="card-header"><h2>Notas</h2></div>
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead><tr><th>Materia</th><th>Ex. 1</th><th>Ex. 2</th><th>Final</th><th>Promedio</th><th>Estado</th></tr></thead>
                <tbody>
                    @foreach($postulante->notas as $nota)
                        <tr>
                            <td>{{ $nota->materia->nombre }}</td>
                            <td>{{ $nota->examen1 ?? '—' }}</td>
                            <td>{{ $nota->examen2 ?? '—' }}</td>
                            <td>{{ $nota->examen3 ?? '—' }}</td>
                            <td><strong>{{ $nota->promedio ?? '—' }}</strong></td>
                            <td><span class="badge {{ $nota->estado === 'APROBADO' ? 'badge-success' : 'badge-danger' }}">{{ $nota->estado ?? 'PENDIENTE' }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Resultado admisión --}}
@if($postulante->admisionFinal)
<div class="card mt-3">
    <div class="card-header"><h2>Resultado de Admisión</h2></div>
    <div class="card-body">
        <div class="alert alert-success">
            <strong>ADMITIDO</strong> en {{ $postulante->admisionFinal->carrera->nombre }} ({{ $postulante->admisionFinal->opcion_ingreso }})
            — Nota final: {{ $postulante->admisionFinal->nota_final_cup }}
        </div>
    </div>
</div>
@endif
@endsection
