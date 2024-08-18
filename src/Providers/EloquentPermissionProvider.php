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

    public function getRolesPermissions(array $roles): Collection
    {
        return parent::getPermissions($roles);
    }

    public function getExtraPermissions(): Collection
    {
        $permissions_attr = config("h-rbac.permissionsAttribute");
        if (isset($this->user->$permissions_attr) && $this->user->$permissions_attr->count()) {
            return $this->user->$permissions_attr;
        } else {
            return collect();
        }
    }

    public function getPermissions(array $roles): Collection
    {
        $role_permissions = $this->getRolesPermissions($roles);
        $user_permissions = collect($role_permissions);
        $extra_permissions = $this->getExtraPermissions();
        if ($extra_permissions->count()) {
            $groups = $extra_permissions->groupBy(['action', 'name']);
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
