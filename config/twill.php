<?php

return [
    'admin_app_url' => env('ADMIN_APP_URL', null),
    'admin_app_path' => ltrim(env('ADMIN_APP_PATH', env('ADMIN_APP_URL', null) ? '' : 'admin'), '/'),
    'admin_app_strict' => env('ADMIN_APP_STRICT', false),


    'file_library' => [
        'allowed_extensions' => ['pdf', 'doc', 'docx', 'mp4', 'jpg', 'jpeg', 'png', 'xlsx'],
        'max_file_size' => 5000000000,
    ],

    'media_library' => [
        'allowed_extensions' => ['svg', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'],
    ],

    'enabled' => [
        'permissions-management' => true,
        'dashboard' => true,
    ],

    'block_editor' => [
        'block_single_layout' => 'layouts.block',
        'repeaters' => [
            'domain' => [
                'title' => 'Domain',
                'trigger' => 'Add Domain',
                'component' => 'a17-block-domain'
            ],

        ],

        'blocks' => [],

        'crops' => [
            'slide_image' => [
                'default' => [
                    [
                        'name' => 'default',
                        'ratio' => 16 / 9,
                    ],
                ],
                'mobile' => [
                    [
                        'name' => 'mobile',
                        'ratio' => 1,
                    ],
                ],
                'flexible' => [
                    [
                        'name' => 'free',
                        'ratio' => 0,
                    ],
                    [
                        'name' => 'landscape',
                        'ratio' => 16 / 9,
                    ],
                    [
                        'name' => 'portrait',
                        'ratio' => 3 / 5,
                    ],
                ],
            ],
        ],
    ],

    'permissions' => [
        'level' => \A17\Twill\Enums\PermissionLevel::LEVEL_ROLE,
        'modules' => [
            'platformSettings',
            'companies',
            'departments',
            'jobRoles',
            'units',
            'pages',
            'menuTypes',
            'menus',
            'countries',
        ]
    ],


    'users_table' => 'twill_users',
    'locales' => ['en']
];
