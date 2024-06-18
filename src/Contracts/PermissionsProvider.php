<?php

namespace Dlnsk\HierarchicalRBAC\Contracts;

use Illuminate\Support\Collection;

interface PermissionsProvider
{
    /**
     * Take a full list of user permissions.
     *
     * @param array $roles
     * @return Collection   The structure is [<permission_name> => <collection of permissions> or NULL, ...].
     *                      Collection is null if permission objects unavailable (user has permission without any additional data).
     */
    public function getPermissions(array $roles): Collection;
}
