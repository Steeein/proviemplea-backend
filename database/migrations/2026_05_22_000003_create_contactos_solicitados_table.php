<?php
// database/migrations/2026_05_22_000003_create_contactos_solicitados_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contactos_solicitados', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // ── Relaciones ────────────────────────────────────────────────────
            $table->foreignUuid('empresa_id')
                  ->constrained('empresas')
                  ->onDelete('restrict'); // No eliminar empresa si tiene contactos

            $table->foreignUuid('persona_id')
                  ->constrained('personas')
                  ->onDelete('restrict'); // No eliminar persona si tiene contactos

            // ── Estado del proceso de selección ───────────────────────────────
            // Flujo: pendiente → contactado → entrevista → seleccionado / no-seleccionado → proceso-cerrado
            $table->string('estado')->default('pendiente');

            // ── Notas internas de administración ──────────────────────────────
            $table->text('notas_admin')->nullable();

            // ── Fechas del proceso (se registran automáticamente) ─────────────
            $table->date('fecha_contacto')->nullable();   // al cambiar a "contactado"
            $table->date('fecha_entrevista')->nullable(); // al cambiar a "entrevista"
            $table->date('fecha_resultado')->nullable();  // al cambiar a "seleccionado"/"no-seleccionado"

            $table->timestamps();

            // ── Índice compuesto para la regla de unicidad de solicitudes activas
            $table->index(['empresa_id', 'persona_id', 'estado'], 'idx_contacto_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos_solicitados');
    }
};
