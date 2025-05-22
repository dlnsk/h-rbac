<?php

namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionChecker;
use Dlnsk\HierarchicalRBAC\Exceptions\PermissionNotFoundException;
use Dlnsk\HierarchicalRBAC\Exceptions\UserHasNoBuiltInRolesException;

class HRBACHelper
{
    public function canUserTakeAbility($user, $ability, $policyClass)
    {
        $permissionChecker = resolve(PermissionChecker::class, compact('user'));
        $permissionChecker->setPolicyBuilder(new PolicyUncallableBuilder());

        return $permissionChecker->check($ability, ['policy' => $policyClass]);
    }

    /**
     * @throws PermissionNotFoundException
     */
    public function getPermissionsPayload($user, $ability, $policyClass)
    {
        $permissionService = resolve(PermissionService::class);
        $policy = $permissionService->getPolicy(['policy' => $policyClass]);
        $policyWrapper = new PolicyWrapper($policy);

        try {
            return $permissionService->getUserPermissionsOfAbility($user, $ability, $policyWrapper);

        } catch (UserHasNoBuiltInRolesException $e) {
            return null;
        } catch (PermissionNotFoundException $e) {
            if (config('h-rbac.riseExceptionIfPermissionNotFound', false)) {
                throw $e;
            } else {
                return null;
            }
        }

    }
}
