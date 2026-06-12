<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('docente', function (Blueprint $table) {
            $table->string('archivo_titulo', 255)->nullable()->default(null);
            $table->string('archivo_maestria', 255)->nullable()->default(null);
            $table->string('archivo_diplomado', 255)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('docente', function (Blueprint $table) {
            $table->dropColumn(['archivo_titulo', 'archivo_maestria', 'archivo_diplomado']);
        });
    }
};
