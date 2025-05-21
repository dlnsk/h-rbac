<?php

namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PolicyBuilder;

class PolicyNormalBuilder implements PolicyBuilder
{
    public function createWrapper($policy): PolicyWrapper
    {
        return new PolicyWrapper($policy);
    }
}
