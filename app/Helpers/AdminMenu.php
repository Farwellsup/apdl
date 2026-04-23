<?php

if (!function_exists('adminMenu')) {
    function adminMenu()
    {
        return [
                 'content' => [
                'title' => 'Content',
                'route' => 'twill.pages.index',
                'primary_navigation' => [
                    'pages' => [
                        'title' => 'Pages',
                        'module' => true,
                    ],
                    'menus' => [
                        'title' => 'Menus',
                        'route' => 'twill.menus.index',
                        'secondary_navigation' => [
                            'menus' => [
                                'title' => 'Menus',
                                'module' => true,
                            ],
                            'menuTypes' => [
                                'title' => 'Menu Types',
                                'module' => true,
                        ]

                    ]
                ],
            ],
         ],
            'companies' => [
                'title' => 'Companies',
                'module' => true,
            ],
            'departments' => [
                'title' => 'Departments',
                'module' => true,
            ],

            'units' => [
                'title' => 'Units',
                'module' => true,
            ],

            // 'jobRoles' => [
            //     'title' => 'Job Roles',
            //     'module' => true,
            // ],

            'settings' => [
                'title' => 'Settings',
                'route' => 'twill.platformSettings.index',
                'primary_navigation' => [
                    'platformSettings' => [
                        'title' => 'Platform Settings',
                        'module' => true,
                    ],
                     'countries' =>[
                'title' => 'Countries',
                'module' => true,
            ]
                    
                ]
            ]
           

        ];
    }
}

if (!function_exists('adminMenuHelper')) {
    function adminMenuHelper()
    {
        if (auth()->guard('twill_users')->check()) {
            $user = auth()->guard('twill_users')->user();

            $possibleSuperAdminValues = ['Owner', 'Administrator', 'Group HR', 'Company HR', 'HOD'];

            if (in_array($user->role_value, $possibleSuperAdminValues) || $user->is_superadmin) {
                return adminMenu();
            }
            return [];
        }

        return [];
    }
}
