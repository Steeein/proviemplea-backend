<?php
// app/Models/ContactoSolicitado.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactoSolicitado extends Model
{
    use HasUuids;

    protected $table = 'contactos_solicitados';

    protected $fillable = [
        'empresa_id', 'persona_id',
        'estado', 'notas_admin',
        'fecha_contacto', 'fecha_entrevista', 'fecha_resultado',
    ];

    protected $casts = [
        'fecha_contacto'  => 'date',
        'fecha_entrevista'=> 'date',
        'fecha_resultado' => 'date',
    ];

    protected $attributes = [
        'estado' => 'pendiente',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Verifica si el proceso de selección está activo
     * (no ha finalizado en no-seleccionado ni proceso-cerrado).
     */
    public function estaActivo(): bool
    {
        return !in_array($this->estado, ['no-seleccionado', 'proceso-cerrado']);
    }
}
