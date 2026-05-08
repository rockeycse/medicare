'default' => 'default',
'documentations' => [
    'default' => [
        'api' => [
            'title' => 'MediCare API',
        ],
        'routes' => [
            'api' => 'api/documentation',
        ],
        'paths' => [
            'use_absolute_path' => true,
            'docs_json' => 'api-docs.json',
            'docs_yaml' => 'api-docs.yaml',
            'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
            'annotations' => [
                base_path('app'),
            ],
        ],
    ],
],
