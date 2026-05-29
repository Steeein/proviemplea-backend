<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Idioma', title: 'Idioma', description: 'Idioma con nivel de dominio del talento')]
class IdiomaSchema
{
    #[OA\Property(property: 'idioma', type: 'string', example: 'Inglés')]
    public string $idioma;

    #[OA\Property(property: 'nivel', type: 'string', enum: ['basico', 'intermedio', 'avanzado', 'nativo'], example: 'avanzado')]
    public string $nivel;
}
