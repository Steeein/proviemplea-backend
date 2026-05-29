<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'EstadoInput', title: 'EstadoInput', required: ['estado'])]
class EstadoInputSchema
{
    #[OA\Property(property: 'estado', type: 'string', enum: ['pendiente', 'contactado', 'entrevista', 'seleccionado', 'no-seleccionado', 'proceso-cerrado'])]
    public string $estado;

    #[OA\Property(property: 'notas_admin', type: 'string', nullable: true)]
    public ?string $notas_admin;
}
