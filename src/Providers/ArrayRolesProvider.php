<?php

namespace Dlnsk\HierarchicalRBAC\Providers;

use Dlnsk\HierarchicalRBAC\Contracts;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ArrayRolesProvider implements Contracts\RolesProvider
{
    public function getUserRoles($user): array
    {
        $roles_attr = config('h-rbac.userRolesAttribute', 'role');

        return Arr::wrap($user->$roles_attr ?? null);
    }

    public function getApplicationRoles(): array
    {
        return array_keys(config('h-rbac.builtinRoles'));
    }

    public function getRolesPermissions($roles): Collection
    {
        $app_roles = config('h-rbac.builtinRoles');
        $user_permissions = [];
        foreach ($roles as $role_name) {
            $user_permissions = array_merge($user_permissions, $app_roles[$role_name] ?? []);
        }

        return collect(array_fill_keys($user_permissions, null));
    }
}
