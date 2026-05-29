<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'PersonaInput', title: 'PersonaInput', description: 'Campos para crear/actualizar talento.')]
class PersonaInputSchema
{
    #[OA\Property(property: 'email', type: 'string', format: 'email', example: 'talento@ejemplo.cl')]
    public string $email;

    #[OA\Property(property: 'telefono', type: 'string', nullable: true, example: '+56912345678')]
    public ?string $telefono;

    #[OA\Property(property: 'resumen', type: 'string', nullable: true)]
    public ?string $resumen;

    #[OA\Property(property: 'nivel_educacional', type: 'string', nullable: true, enum: ['basica', 'media', 'tecnica', 'universitaria', 'postgrado'])]
    public ?string $nivel_educacional;

    #[OA\Property(property: 'titulo_carrera', type: 'string', nullable: true)]
    public ?string $titulo_carrera;

    #[OA\Property(property: 'anio_egreso', type: 'integer', nullable: true, example: 2020)]
    public ?int $anio_egreso;

    #[OA\Property(property: 'anios_experiencia', type: 'integer', nullable: true, example: 3)]
    public ?int $anios_experiencia;

    #[OA\Property(property: 'areas_experiencia', type: 'array', nullable: true, items: new OA\Items(type: 'string'))]
    public ?array $areas_experiencia;

    #[OA\Property(property: 'competencias', type: 'array', nullable: true, items: new OA\Items(type: 'string'))]
    public ?array $competencias;

    #[OA\Property(property: 'rango_renta', type: 'string', nullable: true, example: '800k-1.2M')]
    public ?string $rango_renta;

    #[OA\Property(property: 'tipo_jornada', type: 'string', nullable: true, enum: ['completa', 'part-time', 'por-horas'])]
    public ?string $tipo_jornada;

    #[OA\Property(property: 'modalidad', type: 'string', nullable: true, enum: ['presencial', 'remoto', 'hibrido'])]
    public ?string $modalidad;

    #[OA\Property(property: 'portafolio_url', type: 'string', format: 'uri', nullable: true)]
    public ?string $portafolio_url;

    #[OA\Property(property: 'persona_discapacidad', type: 'boolean', nullable: true, example: false)]
    public ?bool $persona_discapacidad;
}
