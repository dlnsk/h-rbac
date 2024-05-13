<?php

namespace Dlnsk\HierarchicalRBAC\Providers;

use Dlnsk\HierarchicalRBAC\Contracts;
use Illuminate\Database\Eloquent\Model;
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
        $many_roles  = config('h-rbac.manyRolesAttribute');
        $single_role = config('h-rbac.singleRoleAttribute', 'role');

        return Arr::wrap($this->user->$single_role ?? null) ?: Arr::wrap($this->user->$many_roles ?? null);
    }

    public function getApplicationRoles(): array
    {
        return array_keys(config('h-rbac.builtinRoles'));
    }

}
