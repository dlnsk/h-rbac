<?php

namespace Dlnsk\HierarchicalRBAC\Contracts;

interface RolesProvider
{
    public function getUserRoles(): array;
    public function getApplicationRoles(): array;
}
