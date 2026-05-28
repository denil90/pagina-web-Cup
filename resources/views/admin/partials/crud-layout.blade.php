{{-- Partial reutilizable para CRUD simples con tabla y formulario inline --}}
@extends('layouts.app')
@section('title', $titulo ?? 'Listado')
@section('header', $titulo ?? 'Listado')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <h2>{{ $tituloFormulario ?? 'Agregar Nuevo' }}</h2>
    </div>
    <div class="card-body">
        @yield('formulario')
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ $tituloTabla ?? 'Registros' }}</h2>
    </div>
    <div class="card-body">
        @yield('tabla')
    </div>
</div>
@endsection
