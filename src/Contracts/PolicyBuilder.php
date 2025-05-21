<?php

namespace Dlnsk\HierarchicalRBAC\Contracts;

use Dlnsk\HierarchicalRBAC\PolicyWrapper;

interface PolicyBuilder
{
    public function createWrapper($policy): PolicyWrapper;
}
