<?php

namespace Dlnsk\HierarchicalRBAC\Contracts;

interface PermissionChecker
{
    /**
     * The main method which is called to check ability for user.
     *
     * @param string $ability The head ability whose chain we are checking
     * @param mixed $arguments Additional arguments for checking (model, policy and any other data)
     * @return bool|null
     */
    public function check($ability, $arguments): ?bool;

    public function setPolicyBuilder(PolicyBuilder $builder);
}
