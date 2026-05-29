<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Curso', title: 'Curso', description: 'Curso o certificación profesional del talento')]
class CursoSchema
{
    #[OA\Property(property: 'nombre', type: 'string', example: 'AWS Cloud Practitioner')]
    public string $nombre;

    #[OA\Property(property: 'institucion', type: 'string', nullable: true, example: 'Amazon Web Services')]
    public ?string $institucion;

    #[OA\Property(property: 'anio', type: 'integer', nullable: true, example: 2023)]
    public ?int $anio;
}
