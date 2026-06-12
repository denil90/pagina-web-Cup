{{-- Portal de Bienvenida - Sin layout, página independiente --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido — Sistema CUP FICCT</title>
    <meta name="description" content="Portal de acceso al Curso Preuniversitario de la Facultad de Informática, Ciencias de la Computación y Telecomunicaciones">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-header">
            <div class="welcome-logo">🎓 CUP — FICCT</div>
            <div class="welcome-faculty">Facultad de Informática, Ciencias de la Computación y Telecomunicaciones</div>
            <h1 class="welcome-title">Bienvenido al Portal</h1>
            <p class="welcome-subtitle">Seleccione su tipo de cuenta para continuar</p>
        </div>

        <div class="welcome-grid">
            {{-- Postulante --}}
            <a href="{{ route('login') }}?tipo=postulante" class="welcome-card" id="card-postulante">
                <div class="welcome-card-icon">🎓</div>
                <div class="welcome-card-title">Postulante</div>
                <div class="welcome-card-desc">Ingrese o regístrese como estudiante al Curso Preuniversitario</div>
            </a>

            {{-- Docente --}}
            <a href="{{ route('login') }}?tipo=docente" class="welcome-card" id="card-docente">
                <div class="welcome-card-icon">👨‍🏫</div>
                <div class="welcome-card-title">Docente</div>
                <div class="welcome-card-desc">Postule como docente o acceda a su panel de enseñanza</div>
            </a>

            {{-- Administrador --}}
            <a href="{{ route('login') }}?tipo=administrador" class="welcome-card" id="card-administrador">
                <div class="welcome-card-icon">⚙️</div>
                <div class="welcome-card-title">Administrador</div>
                <div class="welcome-card-desc">Acceda al panel de gestión y administración del sistema</div>
            </a>
        </div>

        <div class="welcome-footer">
            <a href="{{ route('resultados.publicos') }}">📊 Consultar Resultados de Admisión</a>
        </div>
    </div>
</body>
</html>
