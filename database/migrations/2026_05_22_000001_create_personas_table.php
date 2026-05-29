<?php
// database/migrations/2026_05_22_000001_create_personas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // ── Datos de contacto (PRIVADOS — no expuestos en CV ciego) ──────
            // Regla de negocio: los empleadores NO ven nombre, email, teléfono
            // ni datos sociodemográficos para evitar discriminación arbitraria.
            $table->string('email')->unique();
            $table->string('telefono', 15)->nullable();
            $table->string('comprobante_residencia')->nullable();

            // ── Datos visibles en CV ciego ────────────────────────────────────
            $table->string('codigo_talento')->unique(); // Ej: PROV-2026-A1B2
            $table->text('resumen')->nullable();

            // ── Educación (sin institución para evitar sesgos) ────────────────
            $table->string('nivel_educacional')->nullable(); // basica|media|tecnica|universitaria|postgrado
            $table->string('titulo_carrera')->nullable();
            $table->integer('anio_egreso')->nullable();

            // ── Experiencia laboral ───────────────────────────────────────────
            $table->integer('anios_experiencia')->default(0);
            $table->json('areas_experiencia')->nullable();  // ["Desarrollo Web","APIs REST"]

            // ── Competencias y condiciones laborales deseadas ─────────────────
            $table->json('competencias')->nullable();        // ["PHP","Laravel","MySQL"]
            $table->string('rango_renta')->nullable();       // "500k-800k"
            $table->string('tipo_jornada')->nullable();      // completa|part-time|por-horas
            $table->string('modalidad')->nullable();         // presencial|remoto|hibrido

            // ── Perfeccionamiento e idiomas ───────────────────────────────────
            $table->json('cursos')->nullable();              // [{nombre, institucion, anio}]
            $table->json('idiomas')->nullable();             // [{idioma, nivel}]

            // ── Extras ────────────────────────────────────────────────────────
            $table->text('portafolio_url')->nullable();
            $table->boolean('persona_discapacidad')->default(false); // Ley 21.015

            // ── Estado ────────────────────────────────────────────────────────
            $table->boolean('validado')->default(false);     // aprobado por administración
            $table->boolean('activo')->default(true);        // false = soft delete
            $table->integer('porcentaje_completitud')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
