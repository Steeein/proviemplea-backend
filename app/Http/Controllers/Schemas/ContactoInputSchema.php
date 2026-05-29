<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ContactoInput', title: 'ContactoInput', required: ['empresa_id', 'persona_id'])]
class ContactoInputSchema
{
    #[OA\Property(property: 'empresa_id', type: 'string', format: 'uuid')]
    public string $empresa_id;

    #[OA\Property(property: 'persona_id', type: 'string', format: 'uuid')]
    public string $persona_id;

    #[OA\Property(property: 'notas_admin', type: 'string', nullable: true)]
    public ?string $notas_admin;
}
