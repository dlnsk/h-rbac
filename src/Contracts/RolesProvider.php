<?php

namespace Dlnsk\HierarchicalRBAC\Contracts;

use Illuminate\Support\Collection;

interface RolesProvider
{
    /**
     * Returns all roles which user has.
     *
     * @param $user
     * @return array
     */
    public function getUserRoles($user): array;


    /**
     * Returns all roles which defined in application.
     *
     * @return array
     */
    public function getApplicationRoles(): array;


    /**
     * Returns all permissions that selected roles have.
     *
     * @param $roles
     * @return Collection
     */
    public function getRolesPermissions($roles): Collection;

}
