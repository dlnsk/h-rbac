<?php

namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PolicyBuilder;

class PolicyUncallableBuilder implements PolicyBuilder
{
    public function createWrapper($policy): PolicyWrapper
    {
        return (new PolicyWrapper($policy))
            ->skipCallbackChecking();
    }
}
