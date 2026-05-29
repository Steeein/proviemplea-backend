<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Empresa', title: 'Empresa', description: 'Empresa empleadora registrada en la plataforma', required: ['id', 'nombre_empresa', 'rut_empresa', 'email', 'tipo_empresa', 'contacto_nombre', 'contacto_email'])]
class EmpresaSchema
{
    #[OA\Property(property: 'id', type: 'string', format: 'uuid')]
    public string $id;

    #[OA\Property(property: 'nombre_empresa', type: 'string', example: 'TechCorp SpA')]
    public string $nombre_empresa;

    #[OA\Property(property: 'rut_empresa', type: 'string', example: '76123456-7')]
    public string $rut_empresa;

    #[OA\Property(property: 'email', type: 'string', format: 'email', example: 'rrhh@techcorp.cl')]
    public string $email;

    #[OA\Property(property: 'logo_url', type: 'string', format: 'uri', nullable: true)]
    public ?string $logo_url;

    #[OA\Property(property: 'rubro', type: 'string', nullable: true, example: 'Tecnología')]
    public ?string $rubro;

    #[OA\Property(property: 'tipo_empresa', type: 'string', enum: ['contratacion-directa', 'est', 'outsourcing'])]
    public string $tipo_empresa;

    #[OA\Property(property: 'presentacion', type: 'string', nullable: true)]
    public ?string $presentacion;

    #[OA\Property(property: 'beneficios', type: 'array', nullable: true, items: new OA\Items(type: 'string'))]
    public ?array $beneficios;

    #[OA\Property(property: 'contacto_nombre', type: 'string', example: 'Ana López')]
    public string $contacto_nombre;

    #[OA\Property(property: 'contacto_email', type: 'string', format: 'email')]
    public string $contacto_email;

    #[OA\Property(property: 'contacto_telefono', type: 'string', nullable: true)]
    public ?string $contacto_telefono;

    #[OA\Property(property: 'validado', type: 'boolean', example: false)]
    public bool $validado;

    #[OA\Property(property: 'activo', type: 'boolean', example: true)]
    public bool $activo;

    #[OA\Property(property: 'created_at', type: 'string', format: 'date-time')]
    public string $created_at;

    #[OA\Property(property: 'updated_at', type: 'string', format: 'date-time')]
    public string $updated_at;
}
