<?php

namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionChecker;

class HRBACHelper
{
    public function canUserTakeAbility($user, $ability, $policyClass)
    {
        $permissionChecker = resolve(PermissionChecker::class, compact('user'));
        $permissionChecker->setPolicyBuilder(new PolicyUncallableBuilder());

        return $permissionChecker->check($ability, ['policy' => $policyClass]);
    }
}
