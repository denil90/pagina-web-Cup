<?php

namespace App\Services;

use App\Models\AdmisionFinal;
use App\Models\Postulante;
use App\Models\Gestion;
use Illuminate\Support\Facades\DB;

class AdmisionService
{
    /**
     * Ejecuta el procedimiento almacenado de admisión final.
     * Delega la lógica compleja (cupos, primera/segunda opción) a PostgreSQL.
     */
    public function procesarAdmision(int $gestionId): array
    {
        try {
            DB::statement('CALL pr_procesar_admision_cup(?)', [$gestionId]);

            $admitidos = AdmisionFinal::whereHas('postulante', function ($query) use ($gestionId) {
                $query->where('id_gestion', $gestionId);
            })->count();

            $totalPostulantes = Postulante::where('id_gestion', $gestionId)->count();

            return [
                'exito' => true,
                'total_postulantes' => $totalPostulantes,
                'total_admitidos' => $admitidos,
                'total_no_admitidos' => $totalPostulantes - $admitidos,
            ];
        } catch (\Exception $e) {
            return [
                'exito' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function obtenerResultados(int $gestionId)
    {
        return AdmisionFinal::with(['postulante.usuario', 'carrera'])
            ->whereHas('postulante', function ($query) use ($gestionId) {
                $query->where('id_gestion', $gestionId);
            })
            ->orderByDesc('nota_final_cup')
            ->get();
    }

    public function verificarAprobacionCompleta(int $postulanteId): bool
    {
        $postulante = Postulante::with('notas')->findOrFail($postulanteId);
        return $postulante->aproboTodasLasMaterias();
    }

    /**
     * Compara estadísticas entre múltiples gestiones para el reporte comparativo.
     */
    public function comparativaGestiones(array $gestionIds): array
    {
        $resultado = [];

        foreach ($gestionIds as $gestionId) {
            $gestion = Gestion::findOrFail($gestionId);
            $totalPostulantes = Postulante::where('id_gestion', $gestionId)->count();
            $admitidos = AdmisionFinal::whereHas('postulante', function ($q) use ($gestionId) {
                $q->where('id_gestion', $gestionId);
            })->count();

            $resultado[] = [
                'gestion' => $gestion->nombreCompleto,
                'id_gestion' => $gestionId,
                'postulantes' => $totalPostulantes,
                'admitidos' => $admitidos,
                'no_admitidos' => $totalPostulantes - $admitidos,
                'tasa_admision' => $totalPostulantes > 0
                    ? round(($admitidos / $totalPostulantes) * 100, 1)
                    : 0,
            ];
        }

        return $resultado;
    }
}
