<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('hasRole')) {
    function hasRole($roles): bool
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return false;
        }

        return in_array($user->role->name, (array) $roles);
    }
}

if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin(): bool
    {
        return hasRole('Owner');
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return hasRole('Administrator');
    }
}

if (!function_exists('isPrivileged')) {
    function isPrivileged(): bool
    {
        return hasRole(['Owner', 'Administrator']);
    }
}
