<?php
// app/Models/Persona.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    use HasUuids;

    protected $fillable = [
        'email', 'telefono', 'comprobante_residencia',
        'codigo_talento', 'resumen',
        'nivel_educacional', 'titulo_carrera', 'anio_egreso',
        'anios_experiencia', 'areas_experiencia',
        'competencias', 'rango_renta', 'tipo_jornada', 'modalidad',
        'cursos', 'idiomas', 'portafolio_url', 'persona_discapacidad',
        'validado', 'activo', 'porcentaje_completitud',
    ];

    protected $casts = [
        'areas_experiencia'   => 'array',
        'competencias'        => 'array',
        'cursos'              => 'array',
        'idiomas'             => 'array',
        'persona_discapacidad'=> 'boolean',
        'validado'            => 'boolean',
        'activo'              => 'boolean',
    ];

    // Ocultar campos privados en respuestas JSON del CV ciego
    // (se usan al hacer ->makeHidden() en el controlador cuando sea necesario)
    protected $hidden = [];

    public function contactos(): HasMany
    {
        return $this->hasMany(ContactoSolicitado::class);
    }
}
