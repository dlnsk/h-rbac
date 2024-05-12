<?php

namespace Dlnsk\HierarchicalRBAC\Providers;

use Dlnsk\HierarchicalRBAC\Contracts;

class ArrayPermissionProvider implements Contracts\PermissionsProvider
{
    public function __construct($user)
    {
    }

    public function getPermissions(array $roles)
    {
        $app_roles = config('h-rbac.builtinRoles');
        $user_permissions = [];
        foreach ($roles as $role_name) {
            $user_permissions = array_merge($user_permissions, $app_roles[$role_name]);
        }

        return $user_permissions;
    }
}
