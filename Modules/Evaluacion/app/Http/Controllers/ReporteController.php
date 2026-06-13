<?php

namespace Modules\Evaluacion\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Academico\Models\Gestion;
use Modules\Planificacion\Models\Grupo;
use Modules\Evaluacion\Services\AdmisionService;
use Modules\Evaluacion\Services\ReporteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReporteController extends Controller
{
    public function __construct(
        private readonly ReporteService $reporteService,
        private readonly AdmisionService $admisionService,
    ) {}

    public function index()
    {
        $gestiones = Gestion::orderByDesc('anio')->orderByDesc('semestre')->get();
        $grupos = Grupo::with('turno')->get();

        return view('evaluacion::reportes.index', compact('gestiones', 'grupos'));
    }

    public function dinamicos(Request $request)
    {
        $gestiones = Gestion::orderByDesc('anio')->orderByDesc('semestre')->get();

        $id_gestion = $request->input('id_gestion');
        if (!$id_gestion && $gestiones->isNotEmpty()) {
            $id_gestion = $gestiones->first()->id_gestion;
        }

        // Obtener listas de ciudades y colegios únicos para los filtros dropdown
        $ciudades = \Illuminate\Support\Facades\DB::table('postulante')
            ->whereNotNull('ciudad')
            ->where('ciudad', '<>', '')
            ->distinct()
            ->orderBy('ciudad')
            ->pluck('ciudad')
            ->toArray();

        $colegios = \Illuminate\Support\Facades\DB::table('postulante')
            ->whereNotNull('colegio_procedencia')
            ->where('colegio_procedencia', '<>', '')
            ->distinct()
            ->orderBy('colegio_procedencia')
            ->pluck('colegio_procedencia')
            ->toArray();

        $ciudadSel = $request->input('ciudad');
        $colegioSel = $request->input('colegio');

        // 1. Admitidos por Carrera para la gestión y filtros seleccionados
        $carrerasData = \Illuminate\Support\Facades\DB::table('carrera as c')
            ->select('c.nombre', 'c.cupo_maximo')
            ->selectSub(function($query) use ($id_gestion, $ciudadSel, $colegioSel) {
                $query->from('admision_final as af')
                    ->join('postulante as p', 'af.id_postulante', '=', 'p.id_postulante')
                    ->whereColumn('af.id_carrera_admitida', 'c.id');
                if ($id_gestion) {
                    $query->where('p.id_gestion', $id_gestion);
                }
                if ($ciudadSel) {
                    $query->where('p.ciudad', $ciudadSel);
                }
                if ($colegioSel) {
                    $query->where('p.colegio_procedencia', $colegioSel);
                }
                $query->selectRaw('COUNT(*)');
            }, 'admitidos')
            ->get();

        // 2. Inscritos por Grupo para la gestión y filtros seleccionados
        $gruposData = \Illuminate\Support\Facades\DB::table('grupo as g')
            ->select('g.nombre', 'g.capacidad_maxima')
            ->selectSub(function($query) use ($id_gestion, $ciudadSel, $colegioSel) {
                $query->from('postulante as p')
                    ->whereColumn('p.id_grupo', 'g.id_grupo');
                if ($id_gestion) {
                    $query->where('p.id_gestion', $id_gestion);
                }
                if ($ciudadSel) {
                    $query->where('p.ciudad', $ciudadSel);
                }
                if ($colegioSel) {
                    $query->where('p.colegio_procedencia', $colegioSel);
                }
                $query->selectRaw('COUNT(*)');
            }, 'inscritos')
            ->get();

        // 3. Comparativa Histórica de Postulantes vs Admitidos con los filtros de ciudad/colegio
        $comparativaData = \Illuminate\Support\Facades\DB::table('gestion as gen')
            ->select('gen.id_gestion', 'gen.semestre', 'gen.anio')
            ->selectSub(function($query) use ($ciudadSel, $colegioSel) {
                $query->from('postulante as p')
                    ->whereColumn('p.id_gestion', 'gen.id_gestion');
                if ($ciudadSel) {
                    $query->where('p.ciudad', $ciudadSel);
                }
                if ($colegioSel) {
                    $query->where('p.colegio_procedencia', $colegioSel);
                }
                $query->selectRaw('COUNT(*)');
            }, 'postulantes')
            ->selectSub(function($query) use ($ciudadSel, $colegioSel) {
                $query->from('admision_final as af')
                    ->join('postulante as p', 'af.id_postulante', '=', 'p.id_postulante')
                    ->whereColumn('p.id_gestion', 'gen.id_gestion');
                if ($ciudadSel) {
                    $query->where('p.ciudad', $ciudadSel);
                }
                if ($colegioSel) {
                    $query->where('p.colegio_procedencia', $colegioSel);
                }
                $query->selectRaw('COUNT(*)');
            }, 'admitidos')
            ->orderBy('gen.anio')
            ->orderBy('gen.semestre')
            ->get();

        // 4. Ranking de Docentes con mayor aprobación (con filtros)
        $docentesRanking = $this->reporteService->docenteConMayorAprobacion((int) $id_gestion, $ciudadSel, $colegioSel);

        // 5. Total de Aprobados vs Reprobados (para un gráfico de dona)
        $totalPostulantesFiltrados = \Illuminate\Support\Facades\DB::table('postulante as p')
            ->where(function($query) use ($id_gestion, $ciudadSel, $colegioSel) {
                if ($id_gestion) {
                    $query->where('p.id_gestion', $id_gestion);
                }
                if ($ciudadSel) {
                    $query->where('p.ciudad', $ciudadSel);
                }
                if ($colegioSel) {
                    $query->where('p.colegio_procedencia', $colegioSel);
                }
            })
            ->count();

        $admitidosFiltrados = \Illuminate\Support\Facades\DB::table('admision_final as af')
            ->join('postulante as p', 'af.id_postulante', '=', 'p.id_postulante')
            ->where(function($query) use ($id_gestion, $ciudadSel, $colegioSel) {
                if ($id_gestion) {
                    $query->where('p.id_gestion', $id_gestion);
                }
                if ($ciudadSel) {
                    $query->where('p.ciudad', $ciudadSel);
                }
                if ($colegioSel) {
                    $query->where('p.colegio_procedencia', $colegioSel);
                }
            })
            ->count();

        $noAdmitidosFiltrados = max(0, $totalPostulantesFiltrados - $admitidosFiltrados);

        return view('evaluacion::reportes.dinamicos', compact(
            'gestiones', 'id_gestion', 'ciudades', 'colegios', 'ciudadSel', 'colegioSel',
            'carrerasData', 'gruposData', 'comparativaData', 'docentesRanking',
            'totalPostulantesFiltrados', 'admitidosFiltrados', 'noAdmitidosFiltrados'
        ));
    }

    /**
     * Procesa una consulta en lenguaje natural usando la API de Gemini.
     * Convierte la pregunta del usuario en una consulta SQL segura de solo lectura.
     */
    public function aiQuery(Request $request)
    {
        $request->validate(['prompt' => 'required|string|max:1000']);

        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return response()->json([
                'error' => true,
                'message' => 'La API Key de Gemini no está configurada. Agrega GEMINI_API_KEY en tu archivo .env',
            ], 422);
        }

        $userPrompt = $request->input('prompt');

        // Construir el prompt del sistema con el esquema de la BD
        $systemPrompt = <<<PROMPT
Eres un asistente de base de datos SQL para un sistema universitario de Cursos Preuniversitarios (CUP) que usa PostgreSQL.
Tu trabajo es convertir preguntas en español a consultas SQL de SOLO LECTURA (SELECT).

ESQUEMA DE LA BASE DE DATOS:

1. usuario (id_usuario PK, nombre, apellidos, ci, correo, fechanac, sexo, direccion, telefono, rol, fecha)
2. carrera (id PK, nombre, descripcion, cupo_maximo)
3. gestion (id_gestion PK, semestre, anio)
4. materia (id_materia PK, nombre, porcentaje_examen1, porcentaje_examen2, porcentaje_examen3)
5. horario (id_horario PK, dia, hora_inicio, hora_final)
6. aula (id_aula PK, nombre, edificio, capacidad)
7. turno (id_turno PK, nombre)
8. grupo (id_grupo PK, nombre, capacidad_maxima, id_horario FK->horario, id_aula FK->aula, id_turno FK->turno)
9. administrador (id_admin PK FK->usuario)
10. docente (id_docente PK FK->usuario, titulo_profesional, maestria, diplomado, estado)
11. postulante (id_postulante PK FK->usuario, colegio_procedencia, ciudad, titulo_bachiller BOOL, libreta_de_ultimo_anio BOOL, archivo_titulo_bachiller, archivo_libreta, id_carrera_primera FK->carrera, id_carrera_segunda FK->carrera, id_grupo FK->grupo, id_gestion FK->gestion, id_turno_preferido FK->turno)
12. docente_grupo (id PK, id_docente FK->docente, id_grupo FK->grupo, id_materia FK->materia, id_horario FK->horario)
13. notas (id_postulante FK->postulante, id_materia FK->materia, examen1, examen2, examen3, promedio, estado, PK(id_postulante, id_materia))
14. admision_final (id_postulante FK->postulante PK, id_carrera_admitida FK->carrera, nota_final_cup, opcion_ingreso)
15. pago (id_pago PK, id_postulante FK->postulante, monto, moneda, paypal_order_id, estado, fecha_pago)

RELACIONES CLAVE:
- postulante.id_postulante = usuario.id_usuario (hereda de usuario)
- docente.id_docente = usuario.id_usuario (hereda de usuario)
- Para obtener el nombre completo de un postulante: JOIN usuario ON postulante.id_postulante = usuario.id_usuario
- Para obtener el nombre completo de un docente: JOIN usuario ON docente.id_docente = usuario.id_usuario
- admision_final contiene los estudiantes admitidos finales
- notas contiene las calificaciones por materia de cada postulante

REGLAS ESTRICTAS:
1. SOLO genera sentencias SELECT. NUNCA generes INSERT, UPDATE, DELETE, DROP, ALTER, CREATE, TRUNCATE, GRANT, REVOKE ni ningún otro DDL/DML.
2. Usa LIMIT 100 para evitar resultados excesivos (a menos que el usuario pida específicamente un conteo).
3. Usa alias legibles en español para las columnas.
4. Si la pregunta no está relacionada con la base de datos, responde con sql vacío.
5. Siempre responde en formato JSON válido.

RESPONDE ÚNICAMENTE con un JSON válido con esta estructura exacta (sin markdown, sin bloques de código):
{"sql": "SELECT ...", "explicacion": "Breve explicación en español de lo que hace la consulta"}

Si no puedes generar una consulta válida, responde:
{"sql": "", "explicacion": "No pude entender la pregunta o no está relacionada con los datos del sistema."}
PROMPT;

        try {
            // Llamar a la API de Gemini con hasta 3 reintentos en caso de fallos temporales
            $response = Http::retry(3, 2000)->timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemPrompt . "\n\nPregunta del usuario: " . $userPrompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 1024,
                    ],
                ]
            );

            if (!$response->successful()) {
                Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
                
                $message = 'Error al comunicarse con la API de Gemini. Código: ' . $response->status();
                if ($response->status() === 429) {
                    $message = 'Límite de solicitudes excedido (Error 429). El plan gratuito de Gemini tiene un límite de consultas por minuto. Por favor, espera un minuto y vuelve a intentarlo.';
                }

                return response()->json([
                    'error' => true,
                    'message' => $message,
                ], $response->status() === 429 ? 429 : 500);
            }

            $body = $response->json();
            $textContent = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Limpiar posibles bloques de código markdown de la respuesta
            $textContent = trim($textContent);
            $textContent = preg_replace('/^```(?:json)?\s*/i', '', $textContent);
            $textContent = preg_replace('/\s*```$/i', '', $textContent);
            $textContent = trim($textContent);

            $aiResult = json_decode($textContent, true);

            if (!$aiResult || !isset($aiResult['sql'])) {
                return response()->json([
                    'error' => true,
                    'message' => 'No se pudo interpretar la respuesta de la IA.',
                    'raw' => $textContent,
                ], 422);
            }

            $sql = trim($aiResult['sql']);
            $explicacion = $aiResult['explicacion'] ?? 'Consulta generada por IA.';

            // Si la IA no pudo generar SQL
            if (empty($sql)) {
                return response()->json([
                    'error' => false,
                    'explicacion' => $explicacion,
                    'sql' => '',
                    'columnas' => [],
                    'resultados' => [],
                ]);
            }

            // ==================== VALIDACIÓN DE SEGURIDAD ====================
            $sqlUpper = strtoupper(preg_replace('/\s+/', ' ', $sql));

            // Debe empezar con SELECT
            if (!preg_match('/^\s*SELECT\b/i', $sql)) {
                return response()->json([
                    'error' => true,
                    'message' => '⛔ La consulta generada no es de solo lectura (SELECT). Operación bloqueada por seguridad.',
                ], 403);
            }

            // Patrones peligrosos prohibidos
            $forbidden = [
                'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'CREATE', 'TRUNCATE',
                'GRANT', 'REVOKE', 'EXECUTE', 'EXEC', 'CALL',
                'PG_SLEEP', 'PG_READ_FILE', 'PG_WRITE_FILE',
                'INFORMATION_SCHEMA', 'PG_CATALOG', 'PG_TABLES',
                'INTO OUTFILE', 'INTO DUMPFILE', 'LOAD_FILE',
                ';',  // no permitir múltiples sentencias
            ];

            foreach ($forbidden as $pattern) {
                if (str_contains($sqlUpper, $pattern)) {
                    return response()->json([
                        'error' => true,
                        'message' => "⛔ La consulta contiene un patrón prohibido ({$pattern}). Operación bloqueada por seguridad.",
                    ], 403);
                }
            }

            // ==================== EJECUCIÓN SEGURA ====================
            $results = DB::select(DB::raw($sql));

            // Convertir a array y extraer las columnas
            $resultsArray = array_map(fn($row) => (array) $row, $results);
            $columnas = !empty($resultsArray) ? array_keys($resultsArray[0]) : [];

            return response()->json([
                'error' => false,
                'explicacion' => $explicacion,
                'sql' => $sql,
                'columnas' => $columnas,
                'resultados' => $resultsArray,
                'total' => count($resultsArray),
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::warning('AI Query SQL error', ['sql' => $sql ?? '', 'error' => $e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => 'Error al ejecutar la consulta SQL: ' . $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('AI Query general error', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function aprobadosPorGestion(Request $request)
    {
        $request->validate(['id_gestion' => 'required|exists:gestion,id_gestion']);

        $gestion = Gestion::findOrFail($request->id_gestion);
        $admitidos = $this->reporteService->aprobadosPorGestion($request->id_gestion);

        return view('evaluacion::reportes.resultado', [
            'titulo' => "Admitidos - {$gestion->nombreCompleto}",
            'tipo' => 'aprobados_gestion',
            'datos' => $admitidos,
            'gestion' => $gestion,
        ]);
    }

    public function rendimientoPorGrupo(Request $request)
    {
        $request->validate(['id_grupo' => 'required|exists:grupo,id_grupo']);

        $grupo = Grupo::with('turno')->findOrFail($request->id_grupo);
        $datos = $this->reporteService->rendimientoPorGrupo($request->id_grupo);

        return view('evaluacion::reportes.resultado', [
            'titulo' => "Rendimiento - Grupo {$grupo->nombre}",
            'tipo' => 'rendimiento_grupo',
            'datos' => $datos,
            'grupo' => $grupo,
        ]);
    }

    public function docenteDestacado(Request $request)
    {
        $request->validate(['id_gestion' => 'required|exists:gestion,id_gestion']);

        $gestion = Gestion::findOrFail($request->id_gestion);
        $ranking = $this->reporteService->docenteConMayorAprobacion($request->id_gestion);

        return view('evaluacion::reportes.resultado', [
            'titulo' => "Ranking Docentes - {$gestion->nombreCompleto}",
            'tipo' => 'docente_destacado',
            'datos' => $ranking,
            'gestion' => $gestion,
        ]);
    }

    public function comparativaGestiones(Request $request)
    {
        $request->validate(['gestiones' => 'required|array|min:2']);

        $datos = $this->admisionService->comparativaGestiones($request->gestiones);

        return view('evaluacion::reportes.resultado', [
            'titulo' => 'Comparativa entre Gestiones',
            'tipo' => 'comparativa',
            'datos' => $datos,
            'gestiones' => $request->gestiones,
        ]);
    }

    public function admitidosPorCarrera(Request $request)
    {
        $request->validate(['id_gestion' => 'required|exists:gestion,id_gestion']);

        $gestion = Gestion::findOrFail($request->id_gestion);
        $datos = $this->reporteService->admitidosPorCarrera($request->id_gestion);

        return view('evaluacion::reportes.resultado', [
            'titulo' => "Admitidos por Carrera - {$gestion->nombreCompleto}",
            'tipo' => 'por_carrera',
            'datos' => $datos,
            'gestion' => $gestion,
        ]);
    }

    public function exportarPdf(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'id_gestion' => 'nullable|exists:gestion,id_gestion',
            'id_grupo' => 'nullable|exists:grupo,id_grupo',
            'gestiones' => 'nullable|array',
        ]);

        $datos = $this->obtenerDatosReporte($request);
        return $this->reporteService->exportarPdf($request->tipo, $datos, "reporte_{$request->tipo}");
    }

    public function exportarCsv(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'id_gestion' => 'nullable|exists:gestion,id_gestion',
        ]);

        $datos = $this->obtenerDatosParaCsv($request);
        return $this->reporteService->exportarCsv($datos['cabeceras'], $datos['filas'], "reporte_{$request->tipo}");
    }

    private function obtenerDatosReporte(Request $request): array
    {
        return match ($request->tipo) {
            'aprobados_gestion' => [
                'admitidos' => $this->reporteService->aprobadosPorGestion($request->id_gestion),
                'gestion' => Gestion::find($request->id_gestion),
            ],
            'rendimiento_grupo' => [
                'rendimiento' => $this->reporteService->rendimientoPorGrupo($request->id_grupo),
                'grupo' => Grupo::with('turno')->find($request->id_grupo),
            ],
            'docente_destacado' => [
                'ranking' => $this->reporteService->docenteConMayorAprobacion($request->id_gestion),
                'gestion' => Gestion::find($request->id_gestion),
            ],
            'comparativa' => [
                'comparativa' => $this->admisionService->comparativaGestiones($request->gestiones ?? []),
            ],
            'por_carrera' => [
                'por_carrera' => $this->reporteService->admitidosPorCarrera($request->id_gestion),
                'gestion' => Gestion::find($request->id_gestion),
            ],
            default => [],
        };
    }

    private function obtenerDatosParaCsv(Request $request): array
    {
        if ($request->tipo === 'aprobados_gestion') {
            $admitidos = $this->reporteService->aprobadosPorGestion($request->id_gestion);
            return [
                'cabeceras' => ['Nombre', 'Apellidos', 'CI', 'Carrera Admitida', 'Nota Final', 'Opción'],
                'filas' => $admitidos->map(fn($a) => [
                    $a->nombre,
                    $a->apellidos,
                    $a->ci,
                    $a->carrera,
                    $a->nota_final_cup,
                    $a->opcion_ingreso,
                ])->toArray(),
            ];
        }

        return ['cabeceras' => [], 'filas' => []];
    }
}
