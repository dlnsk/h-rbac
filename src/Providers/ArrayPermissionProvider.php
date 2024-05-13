<?php

namespace Dlnsk\HierarchicalRBAC\Providers;

use Dlnsk\HierarchicalRBAC\Contracts;

class ArrayPermissionProvider implements Contracts\PermissionsProvider
{
    public function __construct($user)
    {
    }

    public function getPermissions(array $roles): array
    {
        $app_roles = config('h-rbac.builtinRoles');
        $user_permissions = [];
        foreach ($roles as $role_name) {
            $user_permissions = array_merge($user_permissions, $app_roles[$role_name]);
        }

        return array_fill_keys($user_permissions, null);
    }
}
