<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ErrorValidacion', title: 'Error de Validación', description: 'Error con detalle de campos inválidos (HTTP 422)')]
class ErrorValidacionSchema
{
    #[OA\Property(property: 'success', type: 'boolean', example: false)]
    public bool $success;

    #[OA\Property(property: 'message', type: 'string', example: 'Los datos enviados no son válidos.')]
    public string $message;

    #[OA\Property(property: 'errors', type: 'object')]
    public array $errors;
}
