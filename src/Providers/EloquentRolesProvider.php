<?php

namespace Dlnsk\HierarchicalRBAC\Providers;

use Dlnsk\HierarchicalRBAC\Contracts;
use Illuminate\Support\Arr;

class EloquentRolesProvider implements Contracts\RolesProvider
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function getUserRoles(): array
    {
        $roles_attr = config('h-rbac.userRolesAttribute', 'role');

        return Arr::wrap($this->user->$roles_attr ?? null);
    }

    public function getApplicationRoles(): array
    {
        return array_keys(config('h-rbac.builtinRoles'));
    }

}
