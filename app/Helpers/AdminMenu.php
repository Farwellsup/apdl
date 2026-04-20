<?php

if (!function_exists('adminMenu')) {
    function adminMenu()
    {
        return [
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

            'platformSettings' =>[
                'title' => 'Settings',
                'module' => true,
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
