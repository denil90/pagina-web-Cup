<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Docente - Sistema CUP FICCT</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card wide">
            <a href="{{ route('login') }}?tipo=docente" class="back-to-portal">← Volver al Inicio de Sesión</a>

            <div class="auth-title">Postulación Docente</div>
            <p class="auth-subtitle">Complete sus datos para postular como docente del Curso Preuniversitario</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('registro.docente') }}">
                @csrf

                <h3 style="color: var(--accent); margin-bottom: 16px; font-size: 0.95rem;">Datos Personales</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="nombre">Nombres</label>
                        <input type="text" id="nombre" name="nombre" class="form-control"
                               value="{{ old('nombre') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="apellidos">Apellidos</label>
                        <input type="text" id="apellidos" name="apellidos" class="form-control"
                               value="{{ old('apellidos') }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="ci">Carnet de Identidad</label>
                        <input type="text" id="ci" name="ci" class="form-control"
                               value="{{ old('ci') }}" placeholder="Ej: 12345678" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="fechanac">Fecha de Nacimiento</label>
                        <input type="date" id="fechanac" name="fechanac" class="form-control"
                               value="{{ old('fechanac') }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="sexo">Sexo</label>
                        <select id="sexo" name="sexo" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control"
                               value="{{ old('telefono') }}" placeholder="Ej: 70012345">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" class="form-control"
                           value="{{ old('direccion') }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="correo">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-control"
                               value="{{ old('correo') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="contrasena">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" class="form-control"
                               placeholder="Mínimo 6 caracteres" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="contrasena_confirmation">Confirmar Contraseña</label>
                    <input type="password" id="contrasena_confirmation" name="contrasena_confirmation"
                           class="form-control" required>
                </div>



                <div class="alert alert-info mt-2" style="font-size: 0.85rem;">
                    Después de registrarse, deberá subir los documentos de respaldo (título profesional, maestría, diplomado) para que un administrador verifique su postulación.
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">Registrar Postulación</button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}?tipo=docente">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
