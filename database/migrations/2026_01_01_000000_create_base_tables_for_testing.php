<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo crear las tablas si no existen (como en el entorno SQLite de pruebas)
        if (!Schema::hasTable('usuario')) {
            Schema::create('usuario', function (Blueprint $table) {
                $table->increments('id_usuario');
                $table->string('nombre', 100);
                $table->string('apellidos', 100);
                $table->string('ci', 20)->unique();
                $table->string('contrasena', 255);
                $table->date('fechanac');
                $table->char('sexo', 1);
                $table->string('direccion', 255);
                $table->string('telefono', 20)->nullable();
                $table->string('rol', 50);
                $table->string('correo', 150)->unique();
                $table->date('fecha')->nullable();
            });
        }

        if (!Schema::hasTable('docente')) {
            Schema::create('docente', function (Blueprint $table) {
                $table->integer('id_docente')->primary();
                $table->string('titulo_profesional', 150)->nullable();
                $table->string('maestria', 150)->nullable();
                $table->string('diplomado', 150)->nullable();
                $table->string('estado', 50)->nullable();
                $table->foreign('id_docente')->references('id_usuario')->on('usuario')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('materia')) {
            Schema::create('materia', function (Blueprint $table) {
                $table->increments('id_materia');
                $table->string('nombre', 150);
                $table->decimal('porcentaje_examen1', 5, 2)->nullable();
                $table->decimal('porcentaje_examen2', 5, 2)->nullable();
                $table->decimal('porcentaje_examen3', 5, 2)->nullable();
            });
        }

        if (!Schema::hasTable('grupo')) {
            Schema::create('grupo', function (Blueprint $table) {
                $table->increments('id_grupo');
                $table->string('nombre', 100);
                $table->integer('capacidad_maxima');
                $table->integer('id_horario')->nullable();
                $table->integer('id_aula')->nullable();
                $table->integer('id_turno')->nullable();
            });
        }

        if (!Schema::hasTable('postulante')) {
            Schema::create('postulante', function (Blueprint $table) {
                $table->integer('id_postulante')->primary();
                $table->string('colegio_procedencia', 150)->nullable();
                $table->string('ciudad', 100)->nullable();
                $table->boolean('titulo_bachiller')->default(false);
                $table->boolean('libreta_de_ultimo_anio')->default(false);
                $table->string('archivo_titulo_bachiller', 255)->nullable();
                $table->string('archivo_libreta', 255)->nullable();
                $table->integer('id_carrera_primera')->nullable();
                $table->integer('id_carrera_segunda')->nullable();
                $table->integer('id_grupo')->nullable();
                $table->integer('id_gestion')->nullable();
                $table->integer('id_turno_preferido')->nullable();
                $table->foreign('id_postulante')->references('id_usuario')->on('usuario')->onDelete('cascade');
                $table->foreign('id_grupo')->references('id_grupo')->on('grupo')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('docente_grupo')) {
            Schema::create('docente_grupo', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_docente');
                $table->integer('id_grupo');
                $table->integer('id_materia');
                $table->integer('id_horario')->nullable();
                $table->foreign('id_docente')->references('id_docente')->on('docente')->onDelete('cascade');
                $table->foreign('id_grupo')->references('id_grupo')->on('grupo')->onDelete('cascade');
                $table->foreign('id_materia')->references('id_materia')->on('materia')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('notas')) {
            Schema::create('notas', function (Blueprint $table) {
                $table->integer('id_postulante');
                $table->integer('id_materia');
                $table->decimal('examen1', 5, 2)->nullable();
                $table->decimal('examen2', 5, 2)->nullable();
                $table->decimal('examen3', 5, 2)->nullable();
                $table->decimal('promedio', 5, 2)->nullable();
                $table->string('estado', 50)->nullable();
                $table->primary(['id_postulante', 'id_materia']);
                $table->foreign('id_postulante')->references('id_postulante')->on('postulante')->onDelete('cascade');
                $table->foreign('id_materia')->references('id_materia')->on('materia')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
        Schema::dropIfExists('docente_grupo');
        Schema::dropIfExists('postulante');
        Schema::dropIfExists('grupo');
        Schema::dropIfExists('materia');
        Schema::dropIfExists('docente');
        Schema::dropIfExists('usuario');
    }
};
