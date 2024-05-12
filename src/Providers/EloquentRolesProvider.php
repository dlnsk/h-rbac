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
        $user_roles = [];
        if(is_subclass_of($this->user, Model::class)) {
            $many_roles  = config('h-rbac.userRolesAttribute');
            $single_role = config('h-rbac.singleRoleAttribute', 'role');
            $user_roles = Arr::wrap($this->user->$single_role ?? null) ?: Arr::wrap($this->user->$many_roles ?? null);
        }

        return $user_roles;
    }

    public function getApplicationRoles(): array
    {
        return array_keys(config('h-rbac.builtinRoles'));
    }

}
