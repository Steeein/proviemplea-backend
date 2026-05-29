<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Persona', title: 'Persona', description: 'Perfil completo del talento (uso administrativo interno)', required: ['id', 'email', 'codigo_talento'])]
class PersonaSchema
{
    #[OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')]
    public string $id;

    #[OA\Property(property: 'email', type: 'string', format: 'email', example: 'talento@ejemplo.cl')]
    public string $email;

    #[OA\Property(property: 'telefono', type: 'string', nullable: true, example: '+56912345678')]
    public ?string $telefono;

    #[OA\Property(property: 'codigo_talento', type: 'string', example: 'PROV-2026-A1B2')]
    public string $codigo_talento;

    #[OA\Property(property: 'resumen', type: 'string', nullable: true)]
    public ?string $resumen;

    #[OA\Property(property: 'nivel_educacional', type: 'string', nullable: true, enum: ['basica', 'media', 'tecnica', 'universitaria', 'postgrado'])]
    public ?string $nivel_educacional;

    #[OA\Property(property: 'titulo_carrera', type: 'string', nullable: true, example: 'Ingeniería Informática')]
    public ?string $titulo_carrera;

    #[OA\Property(property: 'anio_egreso', type: 'integer', nullable: true, example: 2020)]
    public ?int $anio_egreso;

    #[OA\Property(property: 'anios_experiencia', type: 'integer', example: 5)]
    public int $anios_experiencia;

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

    #[OA\Property(property: 'cursos', type: 'array', nullable: true, items: new OA\Items(ref: '#/components/schemas/Curso'))]
    public ?array $cursos;

    #[OA\Property(property: 'idiomas', type: 'array', nullable: true, items: new OA\Items(ref: '#/components/schemas/Idioma'))]
    public ?array $idiomas;

    #[OA\Property(property: 'portafolio_url', type: 'string', format: 'uri', nullable: true)]
    public ?string $portafolio_url;

    #[OA\Property(property: 'persona_discapacidad', type: 'boolean', example: false)]
    public bool $persona_discapacidad;

    #[OA\Property(property: 'validado', type: 'boolean', example: false)]
    public bool $validado;

    #[OA\Property(property: 'activo', type: 'boolean', example: true)]
    public bool $activo;

    #[OA\Property(property: 'porcentaje_completitud', type: 'integer', minimum: 0, maximum: 100, example: 75)]
    public int $porcentaje_completitud;

    #[OA\Property(property: 'created_at', type: 'string', format: 'date-time')]
    public string $created_at;

    #[OA\Property(property: 'updated_at', type: 'string', format: 'date-time')]
    public string $updated_at;
}
