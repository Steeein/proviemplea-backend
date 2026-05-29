<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class PersonaController extends Controller
{
    private const CACHE_TTL = 300;

    #[OA\Get(
        path: '/personas',
        operationId: 'listarPersonas',
        summary: 'Listar talentos en formato CV ciego',
        description: 'Retorna perfiles activos sin datos identificatorios. **Caché 5 min.** Los empleadores NO tienen acceso a nombre, email, teléfono, edad, género ni comuna de residencia (privacidad para evitar discriminación).',
        tags: ['Personas'],
        parameters: [
            new OA\Parameter(name: 'validado', in: 'query', required: false, schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(
                name: 'nivel_educacional',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['basica', 'media', 'tecnica', 'universitaria', 'postgrado'])
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de CV ciegos (caché 5 min, rate limit 60/min)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/PersonaCVCiego')
                        ),
                    ]
                )
            ),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'personas_list_' . md5(json_encode($request->only(['validado', 'nivel_educacional'])));

        $personas = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
            $query = Persona::where('activo', true);

            if ($request->has('validado')) {
                $query->where('validado', filter_var($request->input('validado'), FILTER_VALIDATE_BOOLEAN));
            }

            if ($request->has('nivel_educacional')) {
                $query->where('nivel_educacional', $request->input('nivel_educacional'));
            }

            return $query->get([
                'id', 'codigo_talento', 'resumen', 'nivel_educacional',
                'titulo_carrera', 'anio_egreso', 'anios_experiencia',
                'areas_experiencia', 'competencias', 'rango_renta',
                'tipo_jornada', 'modalidad', 'idiomas', 'persona_discapacidad',
                'porcentaje_completitud',
            ]);
        });

        return $this->successResponse($personas);
    }

    #[OA\Post(
        path: '/personas',
        operationId: 'crearPersona',
        summary: 'Registrar nuevo talento',
        description: 'Crea un perfil. El `codigo_talento` se genera automáticamente (PROV-YYYY-XXXX). El perfil inicia con `validado=false` y `activo=true`.',
        tags: ['Personas'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PersonaInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Talento registrado exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Persona'),
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
            'email'               => 'required|email|unique:personas,email',
            'telefono'            => 'nullable|string|max:15',
            'resumen'             => 'nullable|string',
            'nivel_educacional'   => 'nullable|in:basica,media,tecnica,universitaria,postgrado',
            'titulo_carrera'      => 'nullable|string',
            'anio_egreso'         => 'nullable|integer|min:1950|max:' . date('Y'),
            'anios_experiencia'   => 'nullable|integer|min:0',
            'areas_experiencia'   => 'nullable|array',
            'areas_experiencia.*' => 'string',
            'competencias'        => 'nullable|array',
            'competencias.*'      => 'string',
            'rango_renta'         => 'nullable|string',
            'tipo_jornada'        => 'nullable|in:completa,part-time,por-horas',
            'modalidad'           => 'nullable|in:presencial,remoto,hibrido',
            'cursos'              => 'nullable|array',
            'cursos.*.nombre'     => 'required_with:cursos|string',
            'cursos.*.institucion'=> 'nullable|string',
            'cursos.*.anio'       => 'nullable|integer|min:1950|max:' . date('Y'),
            'idiomas'             => 'nullable|array',
            'idiomas.*.idioma'    => 'required_with:idiomas|string',
            'idiomas.*.nivel'     => 'required_with:idiomas|in:basico,intermedio,avanzado,nativo',
            'portafolio_url'      => 'nullable|url',
            'persona_discapacidad'=> 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Los datos enviados no son válidos.', 422, $validator->errors()->toArray());
        }

        $data = $validator->validated();

        do {
            $codigo = 'PROV-' . date('Y') . '-' . strtoupper(Str::random(4));
        } while (Persona::where('codigo_talento', $codigo)->exists());

        $data['codigo_talento']         = $codigo;
        $data['porcentaje_completitud'] = $this->calcularCompletitud($data);

        $persona = Persona::create($data);

        Cache::flush();

        return $this->successResponse($persona, 201);
    }

    #[OA\Get(
        path: '/personas/{id}',
        operationId: 'obtenerPersona',
        summary: 'Obtener perfil completo de un talento',
        description: 'Retorna **todos** los datos, incluyendo email y teléfono. Uso administrativo interno.',
        tags: ['Personas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Perfil obtenido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Persona'),
                    ]
                )
            ),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $persona = Persona::find($id);

        if (!$persona) {
            return $this->errorResponse('Persona no encontrada.', 404);
        }

        return $this->successResponse($persona);
    }

    #[OA\Put(
        path: '/personas/{id}',
        operationId: 'actualizarPersona',
        summary: 'Actualizar perfil de talento',
        description: 'Todos los campos opcionales. Recalcula `porcentaje_completitud` automáticamente.',
        tags: ['Personas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PersonaInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Perfil actualizado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Persona'),
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
        $persona = Persona::find($id);

        if (!$persona) {
            return $this->errorResponse('Persona no encontrada.', 404);
        }

        $validator = Validator::make($request->all(), [
            'email'               => 'sometimes|email|unique:personas,email,' . $persona->id,
            'telefono'            => 'nullable|string|max:15',
            'resumen'             => 'nullable|string',
            'nivel_educacional'   => 'nullable|in:basica,media,tecnica,universitaria,postgrado',
            'titulo_carrera'      => 'nullable|string',
            'anio_egreso'         => 'nullable|integer|min:1950|max:' . date('Y'),
            'anios_experiencia'   => 'nullable|integer|min:0',
            'areas_experiencia'   => 'nullable|array',
            'competencias'        => 'nullable|array',
            'rango_renta'         => 'nullable|string',
            'tipo_jornada'        => 'nullable|in:completa,part-time,por-horas',
            'modalidad'           => 'nullable|in:presencial,remoto,hibrido',
            'cursos'              => 'nullable|array',
            'cursos.*.nombre'     => 'required_with:cursos|string',
            'cursos.*.institucion'=> 'nullable|string',
            'cursos.*.anio'       => 'nullable|integer|min:1950|max:' . date('Y'),
            'idiomas'             => 'nullable|array',
            'idiomas.*.idioma'    => 'required_with:idiomas|string',
            'idiomas.*.nivel'     => 'required_with:idiomas|in:basico,intermedio,avanzado,nativo',
            'portafolio_url'      => 'nullable|url',
            'persona_discapacidad'=> 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Los datos enviados no son válidos.', 422, $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $merged = array_merge($persona->toArray(), $data);
        $data['porcentaje_completitud'] = $this->calcularCompletitud($merged);

        $persona->update($data);

        Cache::flush();

        return $this->successResponse($persona->fresh());
    }

    #[OA\Delete(
        path: '/personas/{id}',
        operationId: 'desactivarPersona',
        summary: 'Desactivar perfil de talento (soft delete)',
        description: 'Pone `activo=false`. No elimina el registro de la BD para preservar integridad referencial con `contactos_solicitados`.',
        tags: ['Personas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Perfil desactivado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'message', type: 'string', example: 'Persona desactivada exitosamente.'),
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
        $persona = Persona::find($id);

        if (!$persona) {
            return $this->errorResponse('Persona no encontrada.', 404);
        }

        $persona->update(['activo' => false]);

        Cache::flush();

        return $this->successResponse(['message' => 'Persona desactivada exitosamente.']);
    }

    #[OA\Patch(
        path: '/personas/{id}/validar',
        operationId: 'validarPersona',
        summary: 'Encender o apagar visibilidad del perfil en la vitrina',
        description: '**Toggle** del campo `validado`. Si estaba `false` lo pone `true` (encender), y si estaba `true` lo pone `false` (apagar). Solo perfiles validados aparecen en la vitrina pública de talentos. **No requiere body.**',
        tags: ['Personas'],
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
                        new OA\Property(property: 'data', ref: '#/components/schemas/Persona'),
                    ]
                )
            ),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
            new OA\Response(response: 500, description: 'Error interno del servidor'),
        ]
    )]
    public function validar(string $id): JsonResponse
    {
        $persona = Persona::find($id);

        if (!$persona) {
            return $this->errorResponse('Persona no encontrada.', 404);
        }

        $persona->update(['validado' => !$persona->validado]);

        Cache::flush();

        return $this->successResponse($persona->fresh());
    }

    private function calcularCompletitud(array $data): int
    {
        $campos = [
            'telefono', 'resumen', 'nivel_educacional', 'titulo_carrera',
            'anio_egreso', 'anios_experiencia', 'areas_experiencia',
            'competencias', 'rango_renta', 'tipo_jornada', 'modalidad',
            'cursos', 'idiomas', 'portafolio_url',
        ];

        $completados = 0;

        foreach ($campos as $campo) {
            $valor = $data[$campo] ?? null;
            if (!empty($valor) || $valor === 0 || $valor === false) {
                $completados++;
            }
        }

        return (int) round(($completados / count($campos)) * 100);
    }
}
