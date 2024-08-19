<?php

namespace Dlnsk\HierarchicalRBAC\Providers;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
use Dlnsk\HierarchicalRBAC\Contracts\RolesProvider;
use Illuminate\Support\Collection;

class ArrayPermissionProvider implements PermissionsProvider
{
    public function __construct($user)
    {
    }

    public function getExtraPermissions(): Collection
    {
        return collect();
    }

    public function getPermissions(array $roles): Collection
    {
        $rolesProvider = resolve(RolesProvider::class);

        return $rolesProvider->getRolesPermissions($roles);
    }
}
