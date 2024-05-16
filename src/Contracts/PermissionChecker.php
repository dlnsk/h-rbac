<?php

namespace Dlnsk\HierarchicalRBAC\Contracts;

interface PermissionChecker
{
    public function check($ability, $arguments);
}
