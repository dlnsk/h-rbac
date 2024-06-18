<?php

namespace Dlnsk\HierarchicalRBAC\Contracts;

interface RolesProvider
{
    /**
     * Returns all roles which user has.
     *
     * @return array
     */
    public function getUserRoles(): array;

    /**
     * Returns all roles which defined in application.
     *
     * @return array
     */
    public function getApplicationRoles(): array;
}
