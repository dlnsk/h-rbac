<?php

namespace Dlnsk\HierarchicalRBAC\Contracts;

interface PermissionsProvider
{
    public function getPermissions(array $roles);
}
