@extends('layouts.app')
@section('title', 'Mis Resultados')
@section('header', 'Resultado de Admisión')

@section('content')
<div class="card" style="max-width:600px;margin:0 auto;">
    <div class="card-body text-center">
        @if($admision)
            <div style="font-size:4rem;margin-bottom:16px;"></div>
            <h2 style="color:var(--success-light);font-size:1.8rem;">¡ADMITIDO!</h2>
            <p class="mt-2" style="font-size:1.1rem;">Has sido admitido en:</p>
            <h3 style="color:var(--accent);font-size:1.3rem;">{{ $admision->carrera->nombre }}</h3>
            <p class="mt-2"><span class="badge badge-primary" style="font-size:0.9rem;">{{ $admision->opcion_ingreso }}</span></p>
            <p class="mt-2">Nota Final CUP: <strong style="font-size:1.3rem;color:var(--accent);">{{ $admision->nota_final_cup }}</strong></p>
        @else
            <div style="font-size:4rem;margin-bottom:16px;"></div>
            <h2 style="color:var(--text-muted);font-size:1.3rem;">Resultados Pendientes</h2>
            <p class="text-muted mt-2">El proceso de admisión aún no ha sido ejecutado o no cumpliste los requisitos de admisión.</p>
        @endif
    </div>
</div>
@endsection
