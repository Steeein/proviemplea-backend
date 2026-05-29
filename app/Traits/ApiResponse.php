<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait ApiResponse
 *
 * Estandariza el formato de todas las respuestas JSON de la API.
 * Toda respuesta exitosa incluye { "success": true, "data": ... }
 * Toda respuesta de error incluye { "success": false, "message": ..., "errors"?: ... }
 */
trait ApiResponse
{
    /**
     * Retorna una respuesta JSON de éxito.
     *
     * @param  mixed  $data    Datos a retornar (array, colección, modelo)
     * @param  int    $status  Código HTTP (200 por defecto, 201 para CREATE)
     */
    protected function successResponse(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
        ], $status);
    }

    /**
     * Retorna una respuesta JSON de error.
     *
     * @param  string      $message  Mensaje descriptivo del error
     * @param  int         $status   Código HTTP (404, 422, 409, 500, etc.)
     * @param  array|null  $errors   Errores de validación por campo (solo en 422)
     */
    protected function errorResponse(string $message, int $status, ?array $errors = null): JsonResponse
    {
        $body = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $status);
    }
}
