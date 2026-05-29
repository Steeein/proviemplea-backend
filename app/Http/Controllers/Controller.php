<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use OpenApi\Attributes as OA;

/**
 * Controlador base de ProviEmplea API.
 *
 * Contiene los metadatos globales de la especificación OpenAPI:
 * título, versión, servidor y agrupaciones de tags.
 */
#[OA\Info(
    version: "1.0.0",
    title: "ProviEmplea API",
    description: "API REST para la plataforma de empleo ProviEmplea de Providencia.
    Gestiona talentos, empresas y procesos de selección con curriculum ciego."
)]
#[OA\Server(
    url: "http://localhost:8080/api",
    description: "Servidor de desarrollo local (Docker)"
)]
#[OA\Tag(name: "Health",          description: "Endpoints de observabilidad del sistema")]
#[OA\Tag(name: "Personas",        description: "Gestión de perfiles de talentos/vecinos")]
#[OA\Tag(name: "Empresas",        description: "Gestión de empresas empleadoras")]
#[OA\Tag(name: "Administración",  description: "Gestión administrativa y seguimiento")]
#[OA\Components(
    responses: [
        new OA\Response(
            response: 'NotFound',
            description: 'Recurso no encontrado',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorGeneral')
        ),
        new OA\Response(
            response: 'ValidationError',
            description: 'Error de validación (HTTP 422)',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorValidacion')
        ),
    ]
)]
abstract class Controller
{
    use ApiResponse;
}
