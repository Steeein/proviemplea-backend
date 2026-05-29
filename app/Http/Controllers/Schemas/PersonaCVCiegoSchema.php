<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'PersonaCVCiego', title: 'Persona CV Ciego', description: 'Perfil anónimo sin datos identificatorios.')]
class PersonaCVCiegoSchema
{
    #[OA\Property(property: 'id', type: 'string', format: 'uuid')]
    public string $id;

    #[OA\Property(property: 'codigo_talento', type: 'string', example: 'PROV-2026-A1B2')]
    public string $codigo_talento;

    #[OA\Property(property: 'resumen', type: 'string', nullable: true)]
    public ?string $resumen;

    #[OA\Property(property: 'nivel_educacional', type: 'string', nullable: true, enum: ['basica', 'media', 'tecnica', 'universitaria', 'postgrado'])]
    public ?string $nivel_educacional;

    #[OA\Property(property: 'titulo_carrera', type: 'string', nullable: true)]
    public ?string $titulo_carrera;

    #[OA\Property(property: 'anio_egreso', type: 'integer', nullable: true)]
    public ?int $anio_egreso;

    #[OA\Property(property: 'anios_experiencia', type: 'integer')]
    public int $anios_experiencia;

    #[OA\Property(property: 'areas_experiencia', type: 'array', nullable: true, items: new OA\Items(type: 'string'))]
    public ?array $areas_experiencia;

    #[OA\Property(property: 'competencias', type: 'array', nullable: true, items: new OA\Items(type: 'string'))]
    public ?array $competencias;

    #[OA\Property(property: 'rango_renta', type: 'string', nullable: true)]
    public ?string $rango_renta;

    #[OA\Property(property: 'tipo_jornada', type: 'string', nullable: true, enum: ['completa', 'part-time', 'por-horas'])]
    public ?string $tipo_jornada;

    #[OA\Property(property: 'modalidad', type: 'string', nullable: true, enum: ['presencial', 'remoto', 'hibrido'])]
    public ?string $modalidad;

    #[OA\Property(property: 'idiomas', type: 'array', nullable: true, items: new OA\Items(ref: '#/components/schemas/Idioma'))]
    public ?array $idiomas;

    #[OA\Property(property: 'persona_discapacidad', type: 'boolean')]
    public bool $persona_discapacidad;
}
