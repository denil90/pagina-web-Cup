<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar columna id_turno_preferido a postulante
        if (!Schema::hasColumn('postulante', 'id_turno_preferido')) {
            Schema::table('postulante', function (Blueprint $table) {
                $table->integer('id_turno_preferido')->nullable();
                $table->foreign('id_turno_preferido')->references('id_turno')->on('turno')->onDelete('set null');
            });
        }

        // Agregar columna id_horario a docente_grupo
        if (!Schema::hasColumn('docente_grupo', 'id_horario')) {
            Schema::table('docente_grupo', function (Blueprint $table) {
                $table->integer('id_horario')->nullable();
                $table->foreign('id_horario')->references('id_horario')->on('horario')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('postulante', function (Blueprint $table) {
            $table->dropForeign(['id_turno_preferido']);
            $table->dropColumn('id_turno_preferido');
        });

        Schema::table('docente_grupo', function (Blueprint $table) {
            $table->dropForeign(['id_horario']);
            $table->dropColumn('id_horario');
        });
    }
};
