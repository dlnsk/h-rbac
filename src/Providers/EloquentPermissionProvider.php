<?php

namespace Dlnsk\HierarchicalRBAC\Providers;

use Illuminate\Support\Collection;

class EloquentPermissionProvider extends ArrayPermissionProvider
{
    protected $user;

    public function __construct($user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    public function getPermissions(array $roles): Collection
    {
        $role_permissions = parent::getPermissions($roles);
        $permissions_attr = config("h-rbac.permissionsAttribute");
        $user_permissions = collect($role_permissions);
        if (isset($this->user->$permissions_attr) && $this->user->$permissions_attr->count()) {
            $groups = $this->user->$permissions_attr->groupBy(['action', 'name']);
            $user_permissions = $user_permissions->merge(
                $groups->get('include')
            );
            $user_permissions = $user_permissions->diffKeys(
                $groups->get('exclude')
            );
        }

        return $user_permissions;
    }
}
