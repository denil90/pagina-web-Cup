<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Postulante - Sistema CUP FIC</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card wide">
            <div class="auth-title">📝 Registro de Postulante</div>
            <p class="auth-subtitle">Complete todos los datos para inscribirse al Curso Preuniversitario</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('registro') }}">
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

                <h3 style="color: var(--accent); margin: 24px 0 16px; font-size: 0.95rem;">Datos de Procedencia</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="colegio_procedencia">Colegio de Procedencia</label>
                        <input type="text" id="colegio_procedencia" name="colegio_procedencia" class="form-control"
                               value="{{ old('colegio_procedencia') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="ciudad">Ciudad</label>
                        <input type="text" id="ciudad" name="ciudad" class="form-control"
                               value="{{ old('ciudad') }}" required>
                    </div>
                </div>

                <h3 style="color: var(--accent); margin: 24px 0 16px; font-size: 0.95rem;">Selección de Carrera</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="id_carrera_primera">Primera Opción de Carrera *</label>
                        <select id="id_carrera_primera" name="id_carrera_primera" class="form-control" required>
                            <option value="">Seleccione su primera opción...</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}" {{ old('id_carrera_primera') == $carrera->id ? 'selected' : '' }}>
                                    {{ $carrera->nombre }} (Cupo: {{ $carrera->cupo_maximo }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="id_carrera_segunda">Segunda Opción de Carrera</label>
                        <select id="id_carrera_segunda" name="id_carrera_segunda" class="form-control">
                            <option value="">Seleccione su segunda opción (opcional)...</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}" {{ old('id_carrera_segunda') == $carrera->id ? 'selected' : '' }}>
                                    {{ $carrera->nombre }} (Cupo: {{ $carrera->cupo_maximo }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($gestiones->count() > 1)
                <div class="form-group">
                    <label class="form-label" for="id_gestion">Gestión</label>
                    <select id="id_gestion" name="id_gestion" class="form-control">
                        @foreach($gestiones as $gestion)
                            <option value="{{ $gestion->id_gestion }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $gestion->nombreCompleto }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">Registrarse</button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
