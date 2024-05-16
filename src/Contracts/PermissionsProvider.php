<?php

namespace Dlnsk\HierarchicalRBAC\Contracts;

interface PermissionsProvider
{
    /**
     * Take a full list of user permissions.
     *
     * @param array $roles
     * @return array    The structure is [permission_name => permission_object, ...].
     *                  Permission_object is null if unavailable.
     */
    public function getPermissions(array $roles): array;
}
