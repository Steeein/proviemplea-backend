<?php
namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Estadisticas', title: 'Estadísticas')]
class EstadisticasSchema
{
    #[OA\Property(property: 'total_personas', type: 'integer', example: 45)]
    public int $total_personas;

    #[OA\Property(property: 'personas_validadas', type: 'integer', example: 38)]
    public int $personas_validadas;

    #[OA\Property(property: 'total_empresas', type: 'integer', example: 12)]
    public int $total_empresas;

    #[OA\Property(property: 'empresas_validadas', type: 'integer', example: 10)]
    public int $empresas_validadas;

    #[OA\Property(property: 'contactos_pendientes', type: 'integer', example: 5)]
    public int $contactos_pendientes;

    #[OA\Property(property: 'contactos_en_proceso', type: 'integer', example: 8)]
    public int $contactos_en_proceso;

    #[OA\Property(property: 'contactos_exitosos', type: 'integer', example: 15)]
    public int $contactos_exitosos;
}
