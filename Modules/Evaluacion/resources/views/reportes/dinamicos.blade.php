@extends('layouts.app')
@section('title', 'Reportes Dinámicos')
@section('header', 'Estadísticas y Reportes Dinámicos')

@section('header-actions')
    <div class="btn-group" id="dynamicReportActions" style="display: flex; gap: 8px;">
        <button id="btnExportPdf" class="btn btn-primary btn-sm" onclick="exportPageToPDF()">Exportar PDF (Captura)</button>
        <button id="btnExportCsv" class="btn btn-success btn-sm" onclick="exportTableToCSV('aiResultsTable', 'resultados_consulta_ia.csv')" style="display: none;">Exportar CSV</button>
    </div>
@endsection

@push('styles')
<style>
    /* Constrain layout to prevent horizontal page scrolling and overlapping under the fixed sidebar */
    .app-layout {
        overflow-x: hidden;
        max-width: 100% !important;
    }
    .main-content {
        width: calc(100% - 280px) !important;
        max-width: calc(100% - 280px) !important;
        margin-left: 280px !important;
        overflow-x: hidden;
        box-sizing: border-box;
    }
    @media (max-width: 768px) {
        .main-content {
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 0 !important;
        }
    }

    /* ==================== AI VOICE ASSISTANT ==================== */
    .ai-assistant-card {
        background: linear-gradient(135deg, rgba(26, 29, 46, 0.95), rgba(15, 17, 23, 0.98));
        border: 1px solid rgba(201, 168, 76, 0.3);
        border-radius: 16px;
        overflow: hidden;
        position: relative;
    }
    .ai-assistant-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #c9a84c, #29b6f6, #c9a84c);
        background-size: 200% 100%;
        animation: shimmer 3s ease-in-out infinite;
    }
    @keyframes shimmer {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .ai-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ai-header h2 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #e8eaf6;
        margin: 0;
    }
    .ai-header .ai-badge {
        background: linear-gradient(135deg, rgba(201, 168, 76, 0.2), rgba(41, 182, 246, 0.2));
        color: #c9a84c;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 12px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border: 1px solid rgba(201, 168, 76, 0.3);
    }
    .ai-body {
        padding: 24px;
    }
    .ai-input-row {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }
    .ai-textarea-wrap {
        flex: 1;
    }
    .ai-textarea-wrap label {
        display: block;
        font-size: 0.78rem;
        color: #9fa8da;
        margin-bottom: 6px;
        font-weight: 500;
    }
    #aiPrompt {
        width: 100%;
        min-height: 52px;
        max-height: 120px;
        resize: vertical;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #e8eaf6;
        padding: 12px 16px;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        line-height: 1.5;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    #aiPrompt:focus {
        outline: none;
        border-color: rgba(201, 168, 76, 0.5);
        box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.1);
    }
    #aiPrompt::placeholder {
        color: #5c6bc0;
    }
    .ai-btn-group {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }
    .btn-mic {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        border: 2px solid rgba(239, 83, 80, 0.4);
        background: rgba(239, 83, 80, 0.08);
        color: #ef5350;
        font-size: 1.3rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        position: relative;
    }
    .btn-mic:hover {
        background: rgba(239, 83, 80, 0.15);
        transform: scale(1.08);
    }
    .btn-mic.recording {
        border-color: #ef5350;
        background: rgba(239, 83, 80, 0.2);
        animation: pulse-mic 1.2s ease-in-out infinite;
        box-shadow: 0 0 20px rgba(239, 83, 80, 0.3);
    }
    @keyframes pulse-mic {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 83, 80, 0.4); }
        50% { transform: scale(1.1); box-shadow: 0 0 24px rgba(239, 83, 80, 0.4); }
    }
    .btn-ai-send {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        border: 2px solid rgba(201, 168, 76, 0.4);
        background: rgba(201, 168, 76, 0.08);
        color: #c9a84c;
        font-size: 1.3rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .btn-ai-send:hover {
        background: rgba(201, 168, 76, 0.18);
        transform: scale(1.08);
    }
    .btn-ai-send:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        transform: none;
    }
    .ai-status {
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.78rem;
        color: #5c6bc0;
        min-height: 24px;
    }
    .ai-status .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #5c6bc0;
        flex-shrink: 0;
    }
    .ai-status.listening .dot { background: #ef5350; animation: pulse-dot 1s infinite; }
    .ai-status.processing .dot { background: #c9a84c; animation: pulse-dot 0.6s infinite; }
    .ai-status.success .dot { background: #4caf50; }
    .ai-status.error .dot { background: #ef5350; }

    /* Results Panel */
    .ai-results-panel {
        margin-top: 20px;
        display: none;
    }
    .ai-results-panel.visible {
        display: block;
        animation: fadeIn 0.4s ease;
    }
    .ai-explanation {
        background: rgba(201, 168, 76, 0.06);
        border: 1px solid rgba(201, 168, 76, 0.15);
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 16px;
        font-size: 0.88rem;
        color: #e8d48b;
        line-height: 1.6;
    }
    .ai-explanation .ai-explain-icon {
        margin-right: 6px;
    }
    .ai-sql-block {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
        font-family: 'Courier New', monospace;
        font-size: 0.78rem;
        color: #9fa8da;
        overflow-x: auto;
        white-space: pre-wrap;
        word-break: break-all;
    }
    .ai-sql-toggle {
        cursor: pointer;
        font-size: 0.75rem;
        color: #5c6bc0;
        margin-bottom: 8px;
        user-select: none;
        transition: color 0.2s;
    }
    .ai-sql-toggle:hover { color: #9fa8da; }
    .ai-results-table-wrap {
        max-height: 420px;
        overflow: auto;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
    }
    .ai-results-table-wrap table {
        width: 100%;
        border-collapse: collapse;
    }
    .ai-results-table-wrap thead th {
        position: sticky;
        top: 0;
        background: rgba(26, 29, 46, 0.98);
        padding: 10px 14px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: #c9a84c;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid rgba(201, 168, 76, 0.2);
        white-space: nowrap;
    }
    .ai-results-table-wrap tbody td {
        padding: 10px 14px;
        font-size: 0.82rem;
        color: #e8eaf6;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        white-space: nowrap;
    }
    .ai-results-table-wrap tbody tr:hover {
        background: rgba(201, 168, 76, 0.04);
    }
    .ai-total-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(76, 175, 80, 0.1);
        color: #4caf50;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 16px;
        margin-bottom: 12px;
    }
    .ai-examples {
        margin-top: 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .ai-example-chip {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 0.75rem;
        color: #9fa8da;
        cursor: pointer;
        transition: all 0.2s;
    }
    .ai-example-chip:hover {
        background: rgba(201, 168, 76, 0.1);
        border-color: rgba(201, 168, 76, 0.3);
        color: #c9a84c;
    }
</style>
@endpush

@section('content')
<!-- Dynamic Filters Card -->
<div class="card" style="margin-bottom: 24px; width: 100%; min-width: 0; box-sizing: border-box; overflow: hidden;">
    <div class="card-body" style="padding: 16px 24px; overflow: hidden; width: 100%; max-width: 100%; box-sizing: border-box;">
        <form method="GET" action="{{ route('admin.reportes.dinamicos') }}" id="filterForm">
            <div style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end; width: 100%; max-width: 100%;">
                <!-- Gestión Filter -->
                <div style="flex: 1; min-width: 200px;">
                    <label for="id_gestion" style="display: block; font-size: 0.82rem; font-weight: 500; color: var(--text-secondary); margin-bottom: 6px;">Gestión:</label>
                    <select name="id_gestion" id="id_gestion" class="form-control" onchange="this.form.submit()">
                        @foreach($gestiones as $g)
                            <option value="{{ $g->id_gestion }}" {{ $id_gestion == $g->id_gestion ? 'selected' : '' }}>
                                Gestión {{ $g->semestre }} - {{ $g->anio }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Ciudad Filter -->
                <div style="flex: 1; min-width: 200px;">
                    <label for="ciudad" style="display: block; font-size: 0.82rem; font-weight: 500; color: var(--text-secondary); margin-bottom: 6px;">Ciudad de Procedencia:</label>
                    <select name="ciudad" id="ciudad" class="form-control" onchange="this.form.submit()">
                        <option value="">Todas las Ciudades</option>
                        @foreach($ciudades as $c)
                            <option value="{{ $c }}" {{ $ciudadSel == $c ? 'selected' : '' }}>
                                {{ $c }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Colegio Filter -->
                <div style="flex: 1; min-width: 200px;">
                    <label for="colegio" style="display: block; font-size: 0.82rem; font-weight: 500; color: var(--text-secondary); margin-bottom: 6px;">Colegio de Procedencia:</label>
                    <select name="colegio" id="colegio" class="form-control" onchange="this.form.submit()">
                        <option value="">Todos los Colegios</option>
                        @foreach($colegios as $col)
                            <option value="{{ $col }}" {{ $colegioSel == $col ? 'selected' : '' }}>
                                {{ $col }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Clear Filters Button -->
                @if($ciudadSel || $colegioSel)
                    <div>
                        <a href="{{ route('admin.reportes.dinamicos', ['id_gestion' => $id_gestion]) }}" class="btn btn-secondary btn-sm" style="height: 38px; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                            Limpiar Filtros
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Responsive Flex Layout Container -->
<div style="width: 100%; display: flex; flex-direction: column; gap: 24px; box-sizing: border-box; overflow: hidden;">
    
    <!-- 1. Historical comparison chart (Full Width) -->
    <div class="card" style="width: 100%; min-width: 0; box-sizing: border-box; overflow: hidden;">
        <div class="card-header">
            <h2>Comparativa Histórica de Postulantes vs Admitidos</h2>
        </div>
        <div class="card-body" style="box-sizing: border-box; overflow: hidden; width: 100%; max-width: 100%;">
            <div style="position: relative; height: 320px; width: 100%; max-width: 100%; overflow: hidden; box-sizing: border-box;">
                <canvas id="historicalChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 2. Row 1: Careers and Groups charts (Wraps automatically) -->
    <div style="display: flex; flex-wrap: wrap; gap: 24px; width: 100%; box-sizing: border-box; overflow: hidden;">
        <!-- Careers quota chart -->
        <div class="card" style="flex: 1 1 450px; min-width: 0; box-sizing: border-box; overflow: hidden;">
            <div class="card-header">
                <h2>Admitidos vs Cupo Máximo por Carrera</h2>
            </div>
            <div class="card-body" style="box-sizing: border-box; overflow: hidden; width: 100%; max-width: 100%;">
                <div style="position: relative; height: 280px; width: 100%; max-width: 100%; overflow: hidden; box-sizing: border-box;">
                    <canvas id="careersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Groups capacity chart -->
        <div class="card" style="flex: 1 1 450px; min-width: 0; box-sizing: border-box; overflow: hidden;">
            <div class="card-header">
                <h2>Capacidad y Alumnos Inscritos por Grupo</h2>
            </div>
            <div class="card-body" style="box-sizing: border-box; overflow: hidden; width: 100%; max-width: 100%;">
                <div style="position: relative; height: 280px; width: 100%; max-width: 100%; overflow: hidden; box-sizing: border-box;">
                    <canvas id="groupsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Row 2: Teacher Ranking and General Admission Rate (Wraps automatically) -->
    <div style="display: flex; flex-wrap: wrap; gap: 24px; width: 100%; box-sizing: border-box; overflow: hidden;">
        <!-- Teacher Ranking Chart -->
        <div class="card" style="flex: 1 1 450px; min-width: 0; box-sizing: border-box; overflow: hidden;">
            <div class="card-header">
                <h2>Ranking de Aprobación por Docente</h2>
            </div>
            <div class="card-body" style="box-sizing: border-box; overflow: hidden; width: 100%; max-width: 100%;">
                @if(empty($docentesRanking))
                    <div style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                        No hay datos de docentes disponibles para los filtros seleccionados.
                    </div>
                @else
                    <div style="position: relative; height: 280px; width: 100%; max-width: 100%; overflow: hidden; box-sizing: border-box;">
                        <canvas id="teachersChart"></canvas>
                    </div>
                @endif
            </div>
        </div>

        <!-- General Admission Rate Doughnut Chart -->
        <div class="card" style="flex: 1 1 450px; min-width: 0; box-sizing: border-box; overflow: hidden;">
            <div class="card-header">
                <h2>Distribución General de Admisión</h2>
            </div>
            <div class="card-body" style="box-sizing: border-box; overflow: hidden; width: 100%; max-width: 100%; display: flex; justify-content: center; align-items: center;">
                @if($totalPostulantesFiltrados == 0)
                    <div style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                        No hay datos de postulantes para los filtros seleccionados.
                    </div>
                @else
                    <div style="position: relative; height: 280px; width: 100%; max-width: 320px; overflow: hidden; margin: 0 auto; box-sizing: border-box;">
                        <canvas id="admissionRateChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- AI Voice Assistant Card -->
<div class="ai-assistant-card" style="margin-top: 24px; width: 100%; min-width: 0; box-sizing: border-box;">
    <div class="ai-header">
        <h2>Asistente de Reportes por Voz</h2>
        <span class="ai-badge">Gemini IA</span>
    </div>
    <div class="ai-body">
        <div class="ai-input-row">
            <div class="ai-textarea-wrap">
                <label for="aiPrompt">Pregunta al asistente (por voz o texto):</label>
                <textarea id="aiPrompt" placeholder="Ej: ¿Cuántos postulantes se registraron por ciudad en la gestión 2025?" rows="2"></textarea>
            </div>
            <div class="ai-btn-group">
                <button type="button" class="btn-mic" id="btnMic" title="Hablar por micrófono">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
                </button>
                <button type="button" class="btn-ai-send" id="btnAiSend" title="Enviar consulta">
                    ▶
                </button>
            </div>
        </div>

        <div class="ai-status" id="aiStatus">
            <span class="dot"></span>
            <span class="ai-status-text">Listo — Habla o escribe tu pregunta</span>
        </div>

        <div class="ai-examples" id="aiExamples">
            <span class="ai-example-chip" data-prompt="¿Cuántos postulantes hay por ciudad?">Postulantes por ciudad</span>
            <span class="ai-example-chip" data-prompt="Muéstrame los 10 postulantes con mejor nota final">Top 10 mejores notas</span>
            <span class="ai-example-chip" data-prompt="¿Cuántos admitidos hay por carrera?">Admitidos por carrera</span>
            <span class="ai-example-chip" data-prompt="¿Cuántos estudiantes tiene cada grupo?">Estudiantes por grupo</span>
            <span class="ai-example-chip" data-prompt="Lista de docentes con su título profesional">Docentes y títulos</span>
            <span class="ai-example-chip" data-prompt="¿Cuántos pagos se han completado?">Pagos completados</span>
        </div>

        <!-- Results Panel -->
        <div class="ai-results-panel" id="aiResultsPanel">
            <div class="ai-explanation" id="aiExplanation"></div>

            <div class="ai-sql-toggle" id="aiSqlToggle" onclick="document.getElementById('aiSqlBlock').style.display = document.getElementById('aiSqlBlock').style.display === 'none' ? 'block' : 'none';">Ver/ocultar consulta SQL generada</div>
            <div class="ai-sql-block" id="aiSqlBlock" style="display: none;"></div>

            <div id="aiTotalBadge" class="ai-total-badge" style="display: none;"></div>

            <div class="ai-results-table-wrap" id="aiResultsTableWrap">
                <table id="aiResultsTable">
                    <thead id="aiTableHead"></thead>
                    <tbody id="aiTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colores del tema del sistema
    const accentColor = '#c9a84c';
    const successColor = '#4caf50';
    const successBg = 'rgba(76, 175, 80, 0.4)';
    const dangerColor = '#ef5350';
    const dangerBg = 'rgba(239, 83, 80, 0.4)';
    const infoColor = '#29b6f6';
    const infoBg = 'rgba(41, 182, 246, 0.4)';
    
    // 1. Gráfico Histórico (Postulantes vs Admitidos)
    const compData = @json($comparativaData);
    const compLabels = compData.map(d => `Gestión ${d.semestre}-${d.anio}`);
    const compPostulantes = compData.map(d => d.postulantes);
    const compAdmitidos = compData.map(d => d.admitidos);

    const ctxHist = document.getElementById('historicalChart').getContext('2d');
    new Chart(ctxHist, {
        type: 'bar',
        data: {
            labels: compLabels,
            datasets: [
                {
                    label: 'Postulantes Registrados',
                    data: compPostulantes,
                    backgroundColor: infoBg,
                    borderColor: infoColor,
                    borderWidth: 2,
                    borderRadius: 4
                },
                {
                    label: 'Admitidos Finales',
                    data: compAdmitidos,
                    backgroundColor: 'rgba(201, 168, 76, 0.4)',
                    borderColor: accentColor,
                    borderWidth: 2,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#e8eaf6', font: { family: 'Inter', size: 12 } }
                },
                tooltip: {
                    padding: 12,
                    backgroundColor: '#1a1d2e',
                    titleColor: '#e8eaf6',
                    bodyColor: '#9fa8da',
                    borderColor: 'rgba(201, 168, 76, 0.3)',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#9fa8da' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#9fa8da' }
                }
            }
        }
    });

    // 2. Gráfico de Carreras (Admitidos vs Cupo Máximo)
    const careersData = @json($carrerasData);
    const careerLabels = careersData.map(d => d.nombre);
    const careerAdmitidos = careersData.map(d => d.admitidos);
    const careerCupos = careersData.map(d => d.cupo_maximo);

    const ctxCarr = document.getElementById('careersChart').getContext('2d');
    new Chart(ctxCarr, {
        type: 'bar',
        data: {
            labels: careerLabels,
            datasets: [
                {
                    label: 'Estudiantes Admitidos',
                    data: careerAdmitidos,
                    backgroundColor: 'rgba(76, 175, 80, 0.4)',
                    borderColor: successColor,
                    borderWidth: 2,
                    borderRadius: 4
                },
                {
                    label: 'Cupo Límite',
                    data: careerCupos,
                    backgroundColor: 'rgba(255, 255, 255, 0.05)',
                    borderColor: '#5c6bc0',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    labels: { color: '#e8eaf6', font: { family: 'Inter', size: 11 } }
                },
                tooltip: {
                    padding: 12,
                    backgroundColor: '#1a1d2e',
                    titleColor: '#e8eaf6',
                    bodyColor: '#9fa8da',
                    borderColor: 'rgba(201, 168, 76, 0.3)',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#9fa8da' }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#9fa8da' }
                }
            }
        }
    });

    // 3. Gráfico de Grupos (Inscritos vs Capacidad)
    const groupsData = @json($gruposData);
    const groupLabels = groupsData.map(d => d.nombre);
    const groupInscritos = groupsData.map(d => d.inscritos);
    const groupMax = groupsData.map(d => d.capacidad_maxima);

    const ctxGrp = document.getElementById('groupsChart').getContext('2d');
    new Chart(ctxGrp, {
        type: 'bar',
        data: {
            labels: groupLabels,
            datasets: [
                {
                    label: 'Alumnos Inscritos',
                    data: groupInscritos,
                    backgroundColor: 'rgba(201, 168, 76, 0.4)',
                    borderColor: accentColor,
                    borderWidth: 2,
                    borderRadius: 4
                },
                {
                    label: 'Capacidad Máxima',
                    data: groupMax,
                    backgroundColor: 'rgba(255, 255, 255, 0.03)',
                    borderColor: '#5c6bc0',
                    borderWidth: 1,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#e8eaf6', font: { family: 'Inter', size: 11 } }
                },
                tooltip: {
                    padding: 12,
                    backgroundColor: '#1a1d2e',
                    titleColor: '#e8eaf6',
                    bodyColor: '#9fa8da',
                    borderColor: 'rgba(201, 168, 76, 0.3)',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#9fa8da' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#9fa8da' }
                }
            }
        }
    });

    // 4. Gráfico de Docentes (Ranking de Aprobación)
    const teachersData = @json($docentesRanking);
    if (teachersData && teachersData.length > 0) {
        const teacherLabels = teachersData.map(d => d.docente.usuario.nombreCompleto);
        const teacherPercentages = teachersData.map(d => d.porcentaje);
        const teacherTotals = teachersData.map(d => d.total_estudiantes);
        const teacherAprobados = teachersData.map(d => d.aprobados);

        const ctxTeach = document.getElementById('teachersChart').getContext('2d');
        new Chart(ctxTeach, {
            type: 'bar',
            data: {
                labels: teacherLabels,
                datasets: [
                    {
                        label: '% de Alumnos Aprobados',
                        data: teacherPercentages,
                        backgroundColor: 'rgba(201, 168, 76, 0.4)',
                        borderColor: accentColor,
                        borderWidth: 2,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        labels: { color: '#e8eaf6', font: { family: 'Inter', size: 11 } }
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#1a1d2e',
                        titleColor: '#e8eaf6',
                        bodyColor: '#9fa8da',
                        borderColor: 'rgba(201, 168, 76, 0.3)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                return `% Aprobados: ${teacherPercentages[index]}% (${teacherAprobados[index]}/${teacherTotals[index]} estudiantes)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        min: 0,
                        max: 100,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { 
                            color: '#9fa8da',
                            callback: function(value) { return value + "%"; }
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: '#9fa8da' }
                    }
                }
            }
        });
    }

    // 5. Gráfico de Dona: Distribución de Admisión General
    if (document.getElementById('admissionRateChart')) {
        const admitidosVal = {{ $admitidosFiltrados }};
        const noAdmitidosVal = {{ $noAdmitidosFiltrados }};
        const totalVal = {{ $totalPostulantesFiltrados }};

        const ctxAdm = document.getElementById('admissionRateChart').getContext('2d');
        new Chart(ctxAdm, {
            type: 'doughnut',
            data: {
                labels: ['Admitidos', 'No Admitidos / Pendientes'],
                datasets: [{
                    data: [admitidosVal, noAdmitidosVal],
                    backgroundColor: [successBg, dangerBg],
                    borderColor: [successColor, dangerColor],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#e8eaf6', font: { family: 'Inter', size: 11 } }
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#1a1d2e',
                        titleColor: '#e8eaf6',
                        bodyColor: '#9fa8da',
                        borderColor: 'rgba(201, 168, 76, 0.3)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const val = context.raw;
                                const pct = totalVal > 0 ? ((val / totalVal) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
});

    // ==================== AI VOICE ASSISTANT ====================
    const btnMic = document.getElementById('btnMic');
    const btnAiSend = document.getElementById('btnAiSend');
    const aiPrompt = document.getElementById('aiPrompt');
    const aiStatus = document.getElementById('aiStatus');
    const aiStatusText = aiStatus.querySelector('.ai-status-text');
    const aiResultsPanel = document.getElementById('aiResultsPanel');
    const aiExplanation = document.getElementById('aiExplanation');
    const aiSqlBlock = document.getElementById('aiSqlBlock');
    const aiTotalBadge = document.getElementById('aiTotalBadge');
    const aiTableHead = document.getElementById('aiTableHead');
    const aiTableBody = document.getElementById('aiTableBody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Web Speech API
    let recognition = null;
    let isRecording = false;
    let silenceTimer = null;
    let userStoppedManually = false;

    if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.lang = 'es-ES';
        recognition.interimResults = true;
        recognition.continuous = true;
        recognition.maxAlternatives = 1;

        recognition.onstart = () => {
            isRecording = true;
            userStoppedManually = false;
            btnMic.classList.add('recording');
            setStatus('listening', 'Escuchando... Habla ahora (clic en el micrófono para detener)');
        };

        recognition.onresult = (event) => {
            // Reset silence timer on every new result
            clearTimeout(silenceTimer);

            let finalTranscript = '';
            let interimTranscript = '';

            for (let i = 0; i < event.results.length; i++) {
                if (event.results[i].isFinal) {
                    finalTranscript += event.results[i][0].transcript;
                } else {
                    interimTranscript += event.results[i][0].transcript;
                }
            }

            // Show combined text (final + interim)
            aiPrompt.value = (finalTranscript + interimTranscript).trim();

            // After receiving final results, start a 2.5s silence timer
            if (finalTranscript) {
                setStatus('listening', 'Escuchando... (se enviará automáticamente al dejar de hablar)');
                silenceTimer = setTimeout(() => {
                    if (isRecording) {
                        userStoppedManually = false;
                        recognition.stop();
                    }
                }, 2500);
            }
        };

        recognition.onend = () => {
            isRecording = false;
            clearTimeout(silenceTimer);
            btnMic.classList.remove('recording');

            const text = aiPrompt.value.trim();
            if (text) {
                // Auto-send the query
                setStatus('success', 'Dictado completado. Enviando consulta...');
                setTimeout(() => sendAiQuery(text), 300);
            } else {
                setStatus('', 'Listo — Habla o escribe tu pregunta');
            }
        };

        recognition.onerror = (event) => {
            clearTimeout(silenceTimer);
            isRecording = false;
            btnMic.classList.remove('recording');

            if (event.error === 'no-speech') {
                setStatus('error', 'No se detectó voz. Asegúrate de que el micrófono funcione e intenta de nuevo.');
            } else if (event.error === 'not-allowed') {
                setStatus('error', 'Permiso de micrófono denegado. Haz clic en el ícono de la barra de direcciones y permite el micrófono.');
            } else if (event.error === 'aborted') {
                // User stopped manually, don't show error
                if (!aiPrompt.value.trim()) {
                    setStatus('', 'Listo — Habla o escribe tu pregunta');
                }
            } else {
                setStatus('error', 'Error de reconocimiento: ' + event.error);
            }
        };
    } else {
        // En navegadores que no soportan reconocimiento de voz (como Brave, Firefox por defecto)
        btnMic.style.opacity = '0.6';
        btnMic.style.borderColor = 'rgba(255, 255, 255, 0.15)';
        btnMic.style.background = 'rgba(255, 255, 255, 0.02)';
        btnMic.style.color = '#757575';
        btnMic.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="1" y1="1" x2="23" y2="23"></line><path d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6"></path><path d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.18 1.6"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>';
        btnMic.title = 'Reconocimiento de voz no disponible en este navegador (como Brave). Usa Google Chrome/Edge o escribe tu consulta.';
        
        // Agregar un aviso explicativo en el status
        setTimeout(() => {
            setStatus('', 'Dictado por voz no disponible en este navegador (Brave). Escribe tu consulta manualmente o usa Chrome/Edge.');
        }, 500);
    }

    btnMic.addEventListener('click', () => {
        if (!recognition) {
            alert('El reconocimiento de voz no está soportado o está desactivado en tu navegador actual (Brave bloquea esta característica por privacidad). Puedes escribir tu consulta manualmente en el cuadro de texto, o abrir la página en Google Chrome o Microsoft Edge para usar el dictado por voz.');
            return;
        }
        if (isRecording) {
            userStoppedManually = true;
            clearTimeout(silenceTimer);
            recognition.stop();
        } else {
            aiPrompt.value = '';
            try {
                recognition.start();
            } catch (e) {
                // Already started, ignore
                setStatus('error', 'El micrófono ya está activo o no está disponible.');
            }
        }
    });

    // Send AI Query
    btnAiSend.addEventListener('click', () => {
        const prompt = aiPrompt.value.trim();
        if (!prompt) {
            setStatus('error', 'Escribe o dicta una pregunta primero.');
            return;
        }
        sendAiQuery(prompt);
    });

    // Allow Enter key (without Shift) to submit
    aiPrompt.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            btnAiSend.click();
        }
    });

    // Example chips
    document.querySelectorAll('.ai-example-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            aiPrompt.value = chip.dataset.prompt;
            sendAiQuery(chip.dataset.prompt);
        });
    });

    function setStatus(type, text) {
        aiStatus.className = 'ai-status ' + type;
        aiStatusText.textContent = text;
    }

    async function sendAiQuery(prompt) {
        setStatus('processing', 'Analizando con Gemini IA...');
        btnAiSend.disabled = true;
        aiResultsPanel.classList.remove('visible');
        const exportCsvBtn = document.getElementById('btnExportCsv');
        if (exportCsvBtn) exportCsvBtn.style.display = 'none';

        try {
            const response = await fetch('{{ route("admin.reportes.dinamicos.ai-query") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ prompt }),
            });

            const data = await response.json();

            if (data.error) {
                setStatus('error', data.message);
                return;
            }

            // Show results panel
            aiResultsPanel.classList.add('visible');

            // Explanation
            aiExplanation.innerHTML = escapeHtml(data.explicacion);

            // SQL block
            aiSqlBlock.textContent = data.sql || '(sin consulta)';
            document.getElementById('aiSqlBlock').style.display = 'none';

            // Results table
            aiTableHead.innerHTML = '';
            aiTableBody.innerHTML = '';

            if (data.columnas && data.columnas.length > 0 && data.resultados && data.resultados.length > 0) {
                // Total badge
                aiTotalBadge.style.display = 'inline-flex';
                aiTotalBadge.innerHTML = data.total + ' resultado' + (data.total !== 1 ? 's' : '') + ' encontrado' + (data.total !== 1 ? 's' : '');

                // Headers
                const headRow = document.createElement('tr');
                data.columnas.forEach(col => {
                    const th = document.createElement('th');
                    th.textContent = col.replace(/_/g, ' ');
                    headRow.appendChild(th);
                });
                aiTableHead.appendChild(headRow);

                // Rows
                data.resultados.forEach(row => {
                    const tr = document.createElement('tr');
                    data.columnas.forEach(col => {
                        const td = document.createElement('td');
                        td.textContent = row[col] !== null && row[col] !== undefined ? row[col] : '—';
                        tr.appendChild(td);
                    });
                    aiTableBody.appendChild(tr);
                });

                if (exportCsvBtn) exportCsvBtn.style.display = 'inline-block';
                setStatus('success', 'Consulta ejecutada exitosamente — ' + data.total + ' resultados');
            } else {
                aiTotalBadge.style.display = 'inline-flex';
                aiTotalBadge.innerHTML = '0 resultados encontrados';
                setStatus('success', 'Consulta ejecutada — Sin resultados para los filtros indicados');
            }

        } catch (err) {
            setStatus('error', 'Error de conexión: ' + err.message);
        } finally {
            btnAiSend.disabled = false;
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    window.exportTableToCSV = function(tableId, filename) {
        const table = document.getElementById(tableId);
        if (!table) return;
        
        let csv = [];
        const rows = table.querySelectorAll("tr");
        
        for (let i = 0; i < rows.length; i++) {
            const row = [];
            const cols = rows[i].querySelectorAll("td, th");
            
            for (let j = 0; j < cols.length; j++) {
                let data = cols[j].innerText.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            
            csv.push(row.join(";"));
        }
        
        const csvContent = "\uFEFF" + csv.join("\n");
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    };

    window.exportPageToPDF = function() {
        const element = document.querySelector('.main-content');
        const opt = {
            margin:       [10, 10, 10, 10],
            filename:     'reporte_dinamico.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, letterRendering: true, backgroundColor: '#0f1117' },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' },
            pagebreak:    { mode: ['css', 'legacy'], avoid: '.card' }
        };
        
        const btnGroup = document.getElementById('dynamicReportActions');
        
        // Ocultar botones temporalmente para la captura del PDF
        if (btnGroup) btnGroup.style.opacity = '0';
        
        html2pdf().set(opt).from(element).save().then(() => {
            if (btnGroup) btnGroup.style.opacity = '1';
        });
    };
</script>
@endpush
