<?php

namespace App\Http\Controllers;

use App\Models\ContactoSolicitado;
use App\Models\Empresa;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class AdministracionController extends Controller
{
    private const CACHE_TTL_STATS = 60;

    #[OA\Get(
        path: '/admin/contactos',
        operationId: 'listarContactos',
        summary: 'Listar solicitudes de contacto',
        description: 'Retorna solicitudes con empresa y persona embebidos. Filtrable por estado.',
        tags: ['Administración'],
        parameters: [
            new OA\Parameter(
                name: 'estado',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['pendiente', 'contactado', 'entrevista', 'seleccionado', 'no-seleccionado', 'proceso-cerrado']
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de contactos',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ContactoSolicitado')
                        ),
                    ]
                )
            ),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function listarContactos(Request $request): JsonResponse
    {
        $query = ContactoSolicitado::with(['empresa', 'persona']);

        if ($request->has('estado')) {
            $estadosValidos = ['pendiente', 'contactado', 'entrevista', 'seleccionado', 'no-seleccionado', 'proceso-cerrado'];
            $estado = $request->input('estado');

            if (!in_array($estado, $estadosValidos)) {
                return $this->errorResponse(
                    'Los datos enviados no son válidos.',
                    422,
                    ['estado' => ["El estado '{$estado}' no es válido."]]
                );
            }

            $query->where('estado', $estado);
        }

        return $this->successResponse($query->orderBy('created_at', 'desc')->get());
    }

    #[OA\Post(
        path: '/admin/contactos',
        operationId: 'crearContactoSolicitado',
        summary: 'Registrar solicitud de contacto',
        description: 'Una empresa solicita contactar a un talento. No puede existir solicitud activa previa.',
        tags: ['Administración'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ContactoInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Solicitud creada en estado pendiente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ContactoSolicitado'),
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Ya existe solicitud activa',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorGeneral')
            ),
            new OA\Response(
                response: 422,
                description: 'IDs inválidos',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorValidacion')
            ),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function crearContacto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'empresa_id'  => 'required|uuid|exists:empresas,id',
            'persona_id'  => 'required|uuid|exists:personas,id',
            'notas_admin' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Los datos enviados no son válidos.', 422, $validator->errors()->toArray());
        }

        $existente = ContactoSolicitado::where('empresa_id', $request->empresa_id)
            ->where('persona_id', $request->persona_id)
            ->whereNotIn('estado', ['no-seleccionado', 'proceso-cerrado'])
            ->first();

        if ($existente) {
            return $this->errorResponse('Ya existe una solicitud activa entre esta empresa y talento.', 409);
        }

        $contacto = ContactoSolicitado::create($validator->validated());

        Cache::forget('estadisticas');

        return $this->successResponse($contacto->load(['empresa', 'persona']), 201);
    }

    #[OA\Patch(
        path: '/admin/contactos/{id}/estado',
        operationId: 'actualizarEstadoContacto',
        summary: 'Actualizar estado del proceso de selección',
        description: 'Avanza el estado. Las fechas (contacto, entrevista, resultado) se registran automáticamente.',
        tags: ['Administración'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/EstadoInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Estado actualizado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ContactoSolicitado'),
                    ]
                )
            ),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function actualizarEstado(Request $request, string $id): JsonResponse
    {
        $contacto = ContactoSolicitado::find($id);

        if (!$contacto) {
            return $this->errorResponse('Contacto no encontrado.', 404);
        }

        $validator = Validator::make($request->all(), [
            'estado'      => 'required|in:pendiente,contactado,entrevista,seleccionado,no-seleccionado,proceso-cerrado',
            'notas_admin' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Los datos enviados no son válidos.', 422, $validator->errors()->toArray());
        }

        $data = $validator->validated();

        if ($data['estado'] === 'contactado' && !$contacto->fecha_contacto) {
            $data['fecha_contacto'] = now()->toDateString();
        } elseif ($data['estado'] === 'entrevista' && !$contacto->fecha_entrevista) {
            $data['fecha_entrevista'] = now()->toDateString();
        } elseif (in_array($data['estado'], ['seleccionado', 'no-seleccionado']) && !$contacto->fecha_resultado) {
            $data['fecha_resultado'] = now()->toDateString();
        }

        $contacto->update($data);

        Cache::forget('estadisticas');

        return $this->successResponse($contacto->fresh()->load(['empresa', 'persona']));
    }

    #[OA\Get(
        path: '/admin/estadisticas',
        operationId: 'getEstadisticas',
        summary: 'Estadísticas generales de la plataforma',
        description: 'Resumen para el dashboard del equipo de Providencia. **Caché 1 min.** Incluye totales de personas, empresas y estados de contactos.',
        tags: ['Administración'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Estadísticas generadas (caché 1 min)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Estadisticas'),
                    ]
                )
            ),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function estadisticas(): JsonResponse
    {
        $stats = Cache::remember('estadisticas', self::CACHE_TTL_STATS, function () {
            return [
                'total_personas'        => Persona::count(),
                'personas_activas'      => Persona::where('activo', true)->count(),
                'personas_validadas'    => Persona::where('validado', true)->count(),
                'total_empresas'        => Empresa::count(),
                'empresas_activas'      => Empresa::where('activo', true)->count(),
                'empresas_validadas'    => Empresa::where('validado', true)->count(),
                'contactos_pendientes'  => ContactoSolicitado::where('estado', 'pendiente')->count(),
                'contactos_en_proceso'  => ContactoSolicitado::whereIn('estado', ['contactado', 'entrevista'])->count(),
                'contactos_exitosos'    => ContactoSolicitado::where('estado', 'seleccionado')->count(),
                'contactos_cerrados'    => ContactoSolicitado::where('estado', 'proceso-cerrado')->count(),
            ];
        });

        return $this->successResponse($stats);
    }
}
