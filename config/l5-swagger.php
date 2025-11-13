<?php

return [

    'default' => 'default',
    'documentations' => [
    'default' => [
        'api' => [
            'title' => 'Biblioteca REST API',
        ],
        'routes' => [
            'api' => 'api/documentation',
        ],
        'paths' => [
            'docs_json' => 'api-docs.json',
            'docs_yaml' => 'api-docs.yaml',
            'annotations' => base_path('app'),
        ],
    ],
],

    'defaults' => [
        'routes' => [
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
            'group_options' => [],
        ],

        'paths' => [
            'docs' => storage_path('api-docs'),
            'views' => base_path('resources/views/vendor/l5-swagger'),
            'base' => env('L5_SWAGGER_BASE_PATH', null),
            'excludes' => [],
        ],

        'scanOptions' => [
            'default_processors_configuration' => [],
            'analyser' => null,
            'analysis' => null,
            'processors' => [],
            'pattern' => null,
            'exclude' => [],
            'open_api_spec_version' =>
                env('L5_SWAGGER_OPEN_API_SPEC_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],

        'securityDefinitions' => [
            'bearerAuth' => [
                'type' => 'apiKey',
                'description' => 'Introduce tu token en formato: Bearer {token}',
                'name' => 'Authorization',
                'in' => 'header',
            ],
            'security' => [
                [['bearerAuth' => []]],
            ],
        ],

        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', true),
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
        'proxy' => false,
        'additional_config_url' => null,
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),
        'validator_url' => null,

        'ui' => [
            'display' => [
                'doc_expansion' => 'list',
                'filter' => true,
                'persist_authorization' => true,
                'deep_linking' => true,
            ],
            'customCss' => '
                .topbar-wrapper .link img {
                    content: none !important;
                    display: inline-block !important;
                    width: 50px !important;
                    height: auto !important;
                }
                .topbar-wrapper .link::before {
                    content: "";
                    display: inline-block;
                    background-size: contain;
                    width: 50px;
                    height: 50px;
                    margin-right: 10px;
                    vertical-align: middle;
                }
                .topbar-wrapper span {
                    font-weight: bold;
                    color: #c57aff !important;
                    font-size: 18px;
                }
                .swagger-ui .topbar {
                    background-color: #1f1f2e !important;
                }
                .swagger-ui .scheme-container {
                    background-color: #f8f5ff;
                }
                ',

            'customJs' => '',
            'authorization' => [
                'persist_authorization' =>
                    env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', true),
                'oauth2' => [
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        'constants' => [
            'L5_SWAGGER_CONST_HOST' =>
                env('L5_SWAGGER_CONST_HOST', 'http://127.0.0.1:8000'),
        ],
    ],
];
