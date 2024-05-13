<?php

namespace Dlnsk\HierarchicalRBAC\Providers;

class EloquentPermissionProvider extends ArrayPermissionProvider
{
    protected $user;

    public function __construct($user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    public function getPermissions(array $roles): array
    {
        $role_permissions = parent::getPermissions($roles);
        $permissions_attr = config("h-rbac.permissionsAttribute");
        $user_permissions = collect($role_permissions);
        if (isset($this->user->$permissions_attr) && $this->user->$permissions_attr->count()) {
            $groups = $this->user->$permissions_attr->groupBy('action');
            $user_permissions = $user_permissions->merge(
                collect($groups->get('include'))
                    ->keyBy('name')
            );
            $user_permissions = $user_permissions->diffKeys(
                collect($groups->get('exclude'))
                    ->keyBy('name')
            );
        }

        return $user_permissions->toArray();
    }
}
