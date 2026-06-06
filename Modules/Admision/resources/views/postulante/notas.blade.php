@extends('layouts.app')
@section('title', 'Mis Notas')
@section('header', 'Mis Notas del CUP')

@section('content')
@if($postulante && $postulante->notas->isNotEmpty())
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table>
                <thead><tr><th>Materia</th><th>Examen 1</th><th>Examen 2</th><th>Examen Final</th><th>Promedio</th><th>Estado</th></tr></thead>
                <tbody>
                    @foreach($postulante->notas as $nota)
                        <tr>
                            <td><strong>{{ $nota->materia->nombre }}</strong></td>
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
        <div class="mt-3 text-center">
            <p class="text-muted">Promedio General: <strong class="text-accent">{{ $postulante->promedioGeneral() }}</strong></p>
        </div>
    </div>
</div>
@else
<div class="card"><div class="card-body"><div class="empty-state"><div class="icon"></div><h3>Aún no tienes notas registradas</h3><p>Las notas serán publicadas al finalizar cada examen.</p></div></div></div>
@endif
@endsection
