<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ContactoSolicitado', title: 'ContactoSolicitado', description: 'Solicitud de contacto entre empresa y talento')]
class ContactoSolicitadoSchema
{
    #[OA\Property(property: 'id', type: 'string', format: 'uuid')]
    public string $id;

    #[OA\Property(property: 'empresa_id', type: 'string', format: 'uuid')]
    public string $empresa_id;

    #[OA\Property(property: 'persona_id', type: 'string', format: 'uuid')]
    public string $persona_id;

    #[OA\Property(property: 'estado', type: 'string', enum: ['pendiente', 'contactado', 'entrevista', 'seleccionado', 'no-seleccionado', 'proceso-cerrado'])]
    public string $estado;

    #[OA\Property(property: 'notas_admin', type: 'string', nullable: true)]
    public ?string $notas_admin;

    #[OA\Property(property: 'fecha_contacto', type: 'string', format: 'date', nullable: true)]
    public ?string $fecha_contacto;

    #[OA\Property(property: 'fecha_entrevista', type: 'string', format: 'date', nullable: true)]
    public ?string $fecha_entrevista;

    #[OA\Property(property: 'fecha_resultado', type: 'string', format: 'date', nullable: true)]
    public ?string $fecha_resultado;

    #[OA\Property(property: 'created_at', type: 'string', format: 'date-time')]
    public string $created_at;
}
