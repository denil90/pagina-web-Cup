@extends('layouts.app')
@section('title', 'Iniciar Sesión')

@section('content')
{{-- Override layout for auth pages --}}
@endsection

{{-- Auth pages don't use the app layout --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema CUP FIC</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-title">🎓 CUP - FIC</div>
            <p class="auth-subtitle">Curso Preuniversitario — Facultad de Informática y Computación</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="correo">Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" class="form-control"
                           value="{{ old('correo') }}" placeholder="tu@correo.com" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" class="form-control"
                           placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">Iniciar Sesión</button>
            </form>

            <div class="text-center mt-3">
                <p class="text-muted">¿Eres postulante nuevo?</p>
                <a href="{{ route('registro') }}" class="btn btn-secondary w-100 mt-1">Registrarse como Postulante</a>
            </div>

            <div class="text-center mt-2">
                <a href="{{ route('resultados.publicos') }}" style="font-size: 0.85rem;">
                    📋 Consultar Resultados de Admisión
                </a>
            </div>
        </div>
    </div>
</body>
</html>
