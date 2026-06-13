<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo ?? 'Reporte' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1d2e;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            color: #1a237e;
            margin: 0 0 4px;
        }
        .header h2 {
            font-size: 13px;
            color: #3949ab;
            margin: 0 0 4px;
            font-weight: normal;
        }
        .header .date {
            font-size: 10px;
            color: #666;
        }
        h3 {
            font-size: 14px;
            color: #1a237e;
            border-bottom: 1px solid #c9a84c;
            padding-bottom: 4px;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th {
            background: #1a237e;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 5px 8px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background: #f5f5fa;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #e8f5e9; color: #2e7d32; }
        .badge-danger { background: #ffebee; color: #c62828; }
        .badge-warning { background: #fff8e1; color: #f57f17; }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            padding-top: 8px;
        }
        .stats-box {
            display: inline-block;
            width: 22%;
            text-align: center;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 8px;
            margin: 4px 1%;
        }
        .stats-box .value {
            font-size: 20px;
            font-weight: bold;
            color: #1a237e;
        }
        .stats-box .label {
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Facultad de Informática y Computación</h1>
        <h2>Curso Preuniversitario — {{ $titulo ?? 'Reporte' }}</h2>
        <div class="date">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    @if(isset($datos['admitidos']))
        <h3>Lista de Admitidos</h3>
        @if(isset($datos['gestion']))
            <p>Gestión: <strong>{{ $datos['gestion']->nombreCompleto }}</strong> — Total: <strong>{{ $datos['admitidos']->count() }}</strong></p>
        @endif
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre Completo</th>
                    <th>CI</th>
                    <th>Carrera Admitida</th>
                    <th>Nota Final</th>
                    <th>Opción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['admitidos'] as $i => $a)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $a->nombre }} {{ $a->apellidos }}</td>
                        <td>{{ $a->ci }}</td>
                        <td>{{ $a->carrera }}</td>
                        <td><strong>{{ $a->nota_final_cup }}</strong></td>
                        <td><span class="badge {{ $a->opcion_ingreso === 'PRIMERA OPCION' ? 'badge-success' : 'badge-warning' }}">{{ $a->opcion_ingreso }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif(isset($datos['ranking']))
        <h3>Ranking de Docentes</h3>
        <table>
            <thead>
                <tr><th>#</th><th>Docente</th><th>Estudiantes</th><th>Aprobados</th><th>% Aprobación</th></tr>
            </thead>
            <tbody>
                @foreach($datos['ranking'] as $i => $d)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $d['docente']->usuario->nombreCompleto }}</td>
                        <td>{{ $d['total_estudiantes'] }}</td>
                        <td>{{ $d['aprobados'] }}</td>
                        <td>{{ $d['porcentaje'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif(isset($datos['rendimiento']))
        <h3>Rendimiento por Grupo</h3>
        @if(isset($datos['grupo']))
            <p>Grupo: <strong>{{ $datos['grupo']->nombre }}</strong> — Turno: <strong>{{ $datos['grupo']->turno?->nombre ?? '—' }}</strong></p>
        @endif
        <div style="margin-bottom: 20px; width: 100%;">
            <div class="stats-box"><div class="value">{{ $datos['rendimiento']['total'] }}</div><div class="label">Total</div></div>
            <div class="stats-box"><div class="value">{{ $datos['rendimiento']['aprobados'] }}</div><div class="label">Aprobados</div></div>
            <div class="stats-box"><div class="value">{{ $datos['rendimiento']['reprobados'] }}</div><div class="label">Reprobados</div></div>
            <div class="stats-box"><div class="value">{{ $datos['rendimiento']['porcentaje_aprobacion'] }}%</div><div class="label">% Aprobación</div></div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estudiante</th>
                    <th>CI</th>
                    <th>Promedio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['rendimiento']['postulantes'] as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->nombre }}</td>
                        <td>{{ $p->ci }}</td>
                        <td><strong>{{ $p->promedio }}</strong></td>
                        <td><span class="badge {{ $p->estado === 'APROBADO' ? 'badge-success' : 'badge-danger' }}">{{ $p->estado }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif(isset($datos['comparativa']))
        <h3>Comparativa Histórica entre Gestiones</h3>
        <table>
            <thead>
                <tr>
                    <th>Gestión</th>
                    <th>Postulantes</th>
                    <th>Admitidos</th>
                    <th>No Admitidos</th>
                    <th>Tasa de Admisión</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['comparativa'] as $d)
                    <tr>
                        <td><strong>{{ $d->gestion }}</strong></td>
                        <td>{{ $d->postulantes }}</td>
                        <td><span class="badge badge-success">{{ $d->admitidos }}</span></td>
                        <td><span class="badge badge-danger">{{ $d->no_admitidos }}</span></td>
                        <td><strong>{{ $d->tasa_admision }}%</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif(isset($datos['por_carrera']))
        <h3>Admitidos por Carrera</h3>
        @if(isset($datos['gestion']))
            <p>Gestión: <strong>{{ $datos['gestion']->nombreCompleto }}</strong></p>
        @endif
        <table>
            <thead>
                <tr>
                    <th>Carrera</th>
                    <th>Cupo Máximo</th>
                    <th>Admitidos</th>
                    <th>1ª Opción</th>
                    <th>2ª Opción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['por_carrera'] as $d)
                    <tr>
                        <td><strong>{{ $d->carrera }}</strong></td>
                        <td>{{ $d->cupo_maximo }}</td>
                        <td><span class="badge badge-success">{{ $d->admitidos }}</span></td>
                        <td>{{ $d->primera_opcion }}</td>
                        <td>{{ $d->segunda_opcion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay datos para mostrar en este reporte.</p>
    @endif

    <div class="footer">
        Sistema CUP - FIC | Reporte generado automáticamente | {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
