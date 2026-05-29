<?php

return [
    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'ProviEmplea API',
            ],

            'routes' => [
                'api' => 'api/documentation',
            ],

            'paths' => [
                'base' => env('L5_SWAGGER_BASE_PATH', null),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',

                'annotations' => [
                    base_path('app/Http/Controllers'),
                ],

                'docs' => storage_path('api-docs'),

                'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),

                'views' => base_path('resources/views/vendor/l5-swagger'),

                'excludes' => [],
            ],

            'scanOptions' => [
                'default_processors_configuration' => [],
                'analyser'  => null,
                'analysis'  => null,
                'processors'=> [],
                'pattern'   => null,
                'exclude'   => [],
                'open_api_spec_version' => env('SWAGGER_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
            ],

            'securityDefinitions' => [
                'securitySchemes' => [],
                'security'        => [],
            ],

            'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

            'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

            'proxy' => false,

            'additional_config_url' => null,

            'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

            'validator_url' => null,

            'ui' => [
                'display' => [
                    'doc_expansion'  => env('L5_SWAGGER_UI_DOC_EXPANSION', 'list'),
                    'filter'         => env('L5_SWAGGER_UI_FILTERS', true),
                    'show_extensions'=> env('L5_SWAGGER_UI_SHOW_EXTENSIONS', false),
                    'show_common_extensions' => env('L5_SWAGGER_UI_SHOW_COMMON_EXTENSIONS', false),
                ],
                'authorization' => [
                    'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),
                    'oauth2' => [
                        'use_pkce_with_authorization_code_grant' => false,
                    ],
                ],
            ],

            'constants' => [
                'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://localhost:8080/api'),
            ],
        ],
    ],

    'defaults' => [
        'routes' => [
            'docs'            => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => [
                'api'  => [],
                'asset'=> [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
            'group_options' => [],
        ],

        'paths' => [
            'docs'            => storage_path('api-docs'),
            'docs_json'       => 'api-docs.json',
            'docs_yaml'       => 'api-docs.yaml',
            'views'           => base_path('resources/views/vendor/l5-swagger'),
            'base'            => env('L5_SWAGGER_BASE_PATH', null),
            'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),
            'excludes'        => [],
        ],

        'scanOptions' => [
            'default_processors_configuration' => [],
            'analyser'  => null,
            'analysis'  => null,
            'processors'=> [],
            'pattern'   => null,
            'exclude'   => [],
            'open_api_spec_version' => env('SWAGGER_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],

        'securityDefinitions' => [
            'securitySchemes' => [],
            'security'        => [],
        ],

        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
        'proxy'  => false,
        'additional_config_url' => null,
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),
        'validator_url'   => null,

        'ui' => [
            'display' => [
                'doc_expansion'  => env('L5_SWAGGER_UI_DOC_EXPANSION', 'list'),
                'filter'         => env('L5_SWAGGER_UI_FILTERS', true),
                'show_extensions'=> env('L5_SWAGGER_UI_SHOW_EXTENSIONS', false),
                'show_common_extensions' => env('L5_SWAGGER_UI_SHOW_COMMON_EXTENSIONS', false),
            ],
            'authorization' => [
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),
                'oauth2' => [
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],
    ],
];
