<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateViewsToModules extends Command
{
    protected $signature = 'module:migrate-views {--dry-run : Mostrar qué se haría sin ejecutar}';
    protected $description = 'Migra las vistas de resources/views/ a sus módulos correspondientes';

    /**
     * Mapeo: carpeta de vistas actual → módulo destino
     */
    private array $viewMap = [
        'admin/gestiones'          => 'Academico',
        'admin/carreras'           => 'Academico',
        'admin/materias'           => 'Academico',
        'auth/login.blade.php'     => 'Seguridad',
        'admin/grupos'             => 'Planificacion',
        'admin/configuracion'      => 'Planificacion',
        'admin/docentes'           => 'Facultad',
        'auth/registro.blade.php'  => 'Admision',
        'postulante'               => 'Admision',
        'admin/postulantes'        => 'Admision',
        'admin/notas'              => 'Evaluacion',
        'admin/admision'           => 'Evaluacion',
        'admin/reportes'           => 'Evaluacion',
        'public'                   => 'Evaluacion',
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $basePath = resource_path('views');
        $moved = 0;
        $skipped = 0;

        $this->info('');
        $this->info('📦 Migración de vistas a módulos nWidart');
        $this->info(str_repeat('─', 60));

        if ($dryRun) {
            $this->warn('🔍 Modo DRY-RUN: no se moverá ningún archivo.');
            $this->info('');
        }

        foreach ($this->viewMap as $source => $module) {
            $sourcePath = "{$basePath}/{$source}";
            $modulePath = base_path("Modules/{$module}/resources/views");

            if (!File::exists($sourcePath)) {
                $this->warn("  ⏭  No existe: {$source}");
                $skipped++;
                continue;
            }

            // Determinar el nombre de carpeta destino (sin el prefijo admin/)
            $destFolder = basename($source);
            $destPath = "{$modulePath}/{$destFolder}";

            // Si es un archivo suelto (no directorio)
            if (File::isFile($sourcePath)) {
                $destPath = "{$modulePath}/" . basename($source);
            }

            if ($dryRun) {
                if (File::isDirectory($sourcePath)) {
                    $count = count(File::allFiles($sourcePath));
                    $this->info("  [DRY] 📂 {$source}/ ({$count} archivos) → Modules/{$module}/resources/views/{$destFolder}/");
                } else {
                    $this->info("  [DRY] 📄 {$source} → Modules/{$module}/resources/views/" . basename($source));
                }
            } else {
                File::ensureDirectoryExists(dirname($destPath));

                if (File::isDirectory($sourcePath)) {
                    File::ensureDirectoryExists($destPath);
                    File::copyDirectory($sourcePath, $destPath);
                    $count = count(File::allFiles($destPath));
                    $this->info("  ✅ 📂 {$source}/ ({$count} archivos) → {$module}");
                } else {
                    File::copy($sourcePath, $destPath);
                    $this->info("  ✅ 📄 {$source} → {$module}");
                }
            }
            $moved++;
        }

        $this->info('');
        $this->info(str_repeat('─', 60));
        $this->info("📊 Resultado: {$moved} procesados, {$skipped} omitidos.");

        if (!$dryRun && $moved > 0) {
            $this->newLine();
            $this->warn('⚠️  Los archivos originales NO se eliminaron.');
            $this->warn('   Elimínalos manualmente después de verificar que todo funciona.');
            $this->newLine();
            $this->info('📝 Recuerda actualizar las llamadas view() en los controladores:');
            $this->info("   view('admin.carreras.index')  →  view('academico::carreras.index')");
            $this->info("   view('admin.grupos.index')    →  view('planificacion::grupos.index')");
            $this->info("   view('admin.docentes.index')  →  view('facultad::docentes.index')");
            $this->info("   view('postulante.dashboard')  →  view('admision::postulante.dashboard')");
            $this->info("   view('admin.notas.index')     →  view('evaluacion::notas.index')");
        }

        return Command::SUCCESS;
    }
}
