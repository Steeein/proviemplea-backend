<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ErrorGeneral', title: 'Error General')]
class ErrorGeneralSchema
{
    #[OA\Property(property: 'success', type: 'boolean', example: false)]
    public bool $success;

    #[OA\Property(property: 'message', type: 'string', example: 'Recurso no encontrado.')]
    public string $message;
}
