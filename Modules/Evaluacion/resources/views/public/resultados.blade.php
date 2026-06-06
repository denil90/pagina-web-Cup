<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Resultados - CUP FICCT</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-title">Resultados de Admisión</div>
            <p class="auth-subtitle">Consulte su resultado ingresando su Carnet de Identidad</p>

            <form method="GET" action="{{ route('resultados.publicos') }}">
                <div class="form-group">
                    <label class="form-label">Carnet de Identidad (CI)</label>
                    <input type="text" name="ci" class="form-control" value="{{ request('ci') }}" placeholder="Ingrese su CI..." required autofocus>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">Consultar</button>
            </form>

            @if(request('ci'))
                <div class="mt-3">
                    @if($resultado)
                        <div class="alert alert-success text-center">
                            <strong>¡ADMITIDO!</strong><br>
                            <strong>{{ $resultado->postulante->usuario->nombreCompleto }}</strong><br>
                            Carrera: <strong>{{ $resultado->carrera->nombre }}</strong><br>
                            Nota Final: <strong>{{ $resultado->nota_final_cup }}</strong><br>
                            <span class="badge badge-primary mt-1">{{ $resultado->opcion_ingreso }}</span>
                        </div>
                    @else
                        <div class="alert alert-danger text-center">
                            No se encontraron resultados de admisión para el CI: <strong>{{ request('ci') }}</strong>.<br>
                            Es posible que el proceso de admisión no se haya ejecutado aún o que no fue admitido.
                        </div>
                    @endif
                </div>
            @endif

            <div class="text-center mt-3">
                <a href="{{ route('login') }}">← Volver al Login</a>
            </div>
        </div>
    </div>
</body>
</html>
