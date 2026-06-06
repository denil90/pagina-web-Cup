@extends('layouts.app')
@section('title', 'Nuevo Docente')
@section('header', 'Registrar Nuevo Docente')

@section('content')
<div class="card" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.docentes.store') }}">
            @csrf
            <h3 style="color: var(--accent); margin-bottom: 16px; font-size: 0.95rem;">Datos Personales</h3>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nombres</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos') }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">CI</label>
                    <input type="text" name="ci" class="form-control" value="{{ old('ci') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Fecha Nacimiento</label>
                    <input type="date" name="fechanac" class="form-control" value="{{ old('fechanac') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-control" required>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="contrasena" class="form-control" required>
                </div>
            </div>

            <h3 style="color: var(--accent); margin: 24px 0 16px; font-size: 0.95rem;">Formación Académica</h3>
            <div class="form-group">
                <label class="form-label">Título Profesional *</label>
                <input type="text" name="titulo_profesional" class="form-control" value="{{ old('titulo_profesional') }}" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Maestría</label>
                    <input type="text" name="maestria" class="form-control" value="{{ old('maestria') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Diplomado en Educación Superior</label>
                    <input type="text" name="diplomado" class="form-control" value="{{ old('diplomado') }}">
                </div>
            </div>

            <div class="btn-group mt-2">
                <button type="submit" class="btn btn-primary">Registrar Docente</button>
                <a href="{{ route('admin.docentes.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
