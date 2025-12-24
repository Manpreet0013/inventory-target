<?php

namespace App\Helpers;

class RoleHelper
{
    public static function slug(string $role): string
    {
        return match (strtolower($role)) {
            'admin' => 'admin',
            'inventory manager' => 'inventory-manage',
            'executive' => 'executive',
            'accountant' => 'accountant',
            default => 'user',
        };
    }
}
