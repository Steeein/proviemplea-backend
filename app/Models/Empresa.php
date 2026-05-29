<?php
// app/Models/Empresa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasUuids;

    protected $fillable = [
        'nombre_empresa', 'rut_empresa', 'email',
        'logo_url', 'rubro', 'tipo_empresa', 'presentacion', 'beneficios',
        'contacto_nombre', 'contacto_email', 'contacto_telefono',
        'validado', 'activo',
    ];

    protected $casts = [
        'beneficios' => 'array',
        'validado'   => 'boolean',
        'activo'     => 'boolean',
    ];

    public function contactos(): HasMany
    {
        return $this->hasMany(ContactoSolicitado::class);
    }
}
