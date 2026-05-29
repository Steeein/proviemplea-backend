<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class EmpresaController extends Controller
{
    private const CACHE_TTL = 300;

    #[OA\Get(
        path: '/empresas',
        operationId: 'listarEmpresas',
        summary: 'Listar empresas empleadoras',
        description: 'Retorna todas las empresas activas. Soporta filtro por tipo. **Caché 5 min.**',
        tags: ['Empresas'],
        parameters: [
            new OA\Parameter(
                name: 'tipo_empresa',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['contratacion-directa', 'est', 'outsourcing'])
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de empresas (caché 5 min, rate limit 60/min)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Empresa')
                        ),
                    ]
                )
            ),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'empresas_list_' . md5(json_encode($request->only(['tipo_empresa'])));

        $empresas = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
            $query = Empresa::where('activo', true);

            if ($request->has('tipo_empresa')) {
                $query->where('tipo_empresa', $request->input('tipo_empresa'));
            }

            return $query->orderBy('nombre_empresa')->get();
        });

        return $this->successResponse($empresas);
    }

    #[OA\Post(
        path: '/empresas',
        operationId: 'crearEmpresa',
        summary: 'Registrar nueva empresa',
        description: '`rut_empresa` y `email` deben ser únicos. Se crea con `validado=false`.',
        tags: ['Empresas'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/EmpresaInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Empresa registrada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Empresa'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorValidacion')
            ),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre_empresa'    => 'required|string|max:255',
            'rut_empresa'       => 'required|string|unique:empresas,rut_empresa',
            'email'             => 'required|email|unique:empresas,email',
            'tipo_empresa'      => 'required|in:contratacion-directa,est,outsourcing',
            'rubro'             => 'nullable|string',
            'presentacion'      => 'nullable|string',
            'beneficios'        => 'nullable|array',
            'beneficios.*'      => 'string',
            'contacto_nombre'   => 'required|string',
            'contacto_email'    => 'required|email',
            'contacto_telefono' => 'nullable|string|max:20',
            'logo_url'          => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Los datos enviados no son válidos.', 422, $validator->errors()->toArray());
        }

        $empresa = Empresa::create($validator->validated());

        Cache::flush();

        return $this->successResponse($empresa, 201);
    }

    #[OA\Get(
        path: '/empresas/{id}',
        operationId: 'obtenerEmpresa',
        summary: 'Obtener empresa por ID',
        tags: ['Empresas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Empresa obtenida',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Empresa'),
                    ]
                )
            ),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return $this->errorResponse('Empresa no encontrada.', 404);
        }

        return $this->successResponse($empresa);
    }

    #[OA\Put(
        path: '/empresas/{id}',
        operationId: 'actualizarEmpresa',
        summary: 'Actualizar datos de empresa',
        description: 'Todos los campos opcionales. `rut_empresa` y `email` deben ser únicos.',
        tags: ['Empresas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/EmpresaInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Empresa actualizada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Empresa'),
                    ]
                )
            ),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function update(Request $request, string $id): JsonResponse
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return $this->errorResponse('Empresa no encontrada.', 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_empresa'    => 'sometimes|string|max:255',
            'rut_empresa'       => 'sometimes|string|unique:empresas,rut_empresa,' . $empresa->id,
            'email'             => 'sometimes|email|unique:empresas,email,' . $empresa->id,
            'tipo_empresa'      => 'sometimes|in:contratacion-directa,est,outsourcing',
            'rubro'             => 'nullable|string',
            'presentacion'      => 'nullable|string',
            'beneficios'        => 'nullable|array',
            'beneficios.*'      => 'string',
            'contacto_nombre'   => 'sometimes|string',
            'contacto_email'    => 'sometimes|email',
            'contacto_telefono' => 'nullable|string|max:20',
            'logo_url'          => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Los datos enviados no son válidos.', 422, $validator->errors()->toArray());
        }

        $empresa->update($validator->validated());

        Cache::flush();

        return $this->successResponse($empresa->fresh());
    }

    #[OA\Delete(
        path: '/empresas/{id}',
        operationId: 'desactivarEmpresa',
        summary: 'Desactivar empresa (soft delete)',
        description: 'Pone `activo=false`. Preserva integridad de `contactos_solicitados`.',
        tags: ['Empresas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Empresa desactivada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'message', type: 'string', example: 'Empresa desactivada exitosamente.'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return $this->errorResponse('Empresa no encontrada.', 404);
        }

        $empresa->update(['activo' => false]);

        Cache::flush();

        return $this->successResponse(['message' => 'Empresa desactivada exitosamente.']);
    }

    #[OA\Patch(
        path: '/empresas/{id}/validar',
        operationId: 'validarEmpresa',
        summary: 'Encender o apagar validación de empresa',
        description: '**Toggle** del campo `validado`. Enciende la empresa si estaba apagada, o la apaga si estaba encendida. Solo empresas validadas pueden buscar talentos. **No requiere body.**',
        tags: ['Empresas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Estado de validación cambiado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Empresa'),
                    ]
                )
            ),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function validar(string $id): JsonResponse
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return $this->errorResponse('Empresa no encontrada.', 404);
        }

        $empresa->update(['validado' => !$empresa->validado]);

        Cache::flush();

        return $this->successResponse($empresa->fresh());
    }
}
