<?php

namespace Dlnsk\HierarchicalRBAC\Providers;

use Dlnsk\HierarchicalRBAC\Contracts;
use Illuminate\Support\Collection;

class ArrayPermissionProvider implements Contracts\PermissionsProvider
{
    public function __construct($user)
    {
    }

    public function getPermissions(array $roles): Collection
    {
        $app_roles = config('h-rbac.builtinRoles');
        $user_permissions = [];
        foreach ($roles as $role_name) {
            $user_permissions = array_merge($user_permissions, $app_roles[$role_name]);
        }

        return collect(array_fill_keys($user_permissions, null));
    }
}
