<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'HealthResponse', title: 'Health Response')]
class HealthResponseSchema
{
    #[OA\Property(property: 'status', type: 'string', enum: ['online', 'degraded', 'offline'])]
    public string $status;

    #[OA\Property(property: 'service', type: 'string', example: 'ProviEmplea API')]
    public string $service;

    #[OA\Property(property: 'version', type: 'string', example: '1.0.0')]
    public string $version;

    #[OA\Property(property: 'timestamp', type: 'string', format: 'date-time')]
    public string $timestamp;
}
