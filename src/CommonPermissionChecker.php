<?php

namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
use Dlnsk\HierarchicalRBAC\Contracts\RolesProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CommonPermissionChecker implements Contracts\PermissionChecker
{
    /**
     * @var RolesProvider
     */
    private $rolesProvider;
    /**
     * @var PermissionsProvider
     */
    private $permissionsProvider;
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
        $this->rolesProvider = resolve(RolesProvider::class, compact('user'));
        $this->permissionsProvider = resolve(PermissionsProvider::class, compact('user'));
    }

    public function check($ability, $arguments)
    {
        $user_roles = $this->rolesProvider->getUserRoles();

        if (in_array('admin', $user_roles)) {
            return true;
        }

        $application_roles = $this->rolesProvider->getApplicationRoles();
        $roles_intersection = array_intersect($application_roles, $user_roles);
        if (!count($roles_intersection)) {
            // User has no application's built-in roles
            return null;
        }
        $user_permissions = $this->permissionsProvider->getPermissions($roles_intersection);

        return $this->checkAbility($user_permissions, $ability, $arguments);
    }

    /**
     * Checking permission for chose user
     *
     * @return boolean
     */
    public function checkAbility($user_permissions, $ability, $arguments)
    {
        $arg1 = head($arguments);
        $policy = Gate::getPolicyFor($arg1);
        // Allow to use policy's classes in Gate, not just models
        if (!$policy && class_exists($arg1) && Str::endsWith($arg1, 'Policy')) {
            $policy = app()->make($arg1);
        }

        $policyWrp = new PolicyWrapper($policy);
        if (!$policyWrp->isValid() || !$policyWrp->hasAbility($ability)) {
            return null;
        }

        $chain = $policyWrp->getChainFor($ability);
        $permission_intersection = array_intersect($chain, $user_permissions);
        foreach ($permission_intersection as $permission) {
            $callback_name = $policyWrp->getCallbackName($permission);
            if ($callback_name) {
                if (!empty($arguments)) {
                    // Pass arguments as array if it has more than one element, or it's associative array
                    $arg = (count($arguments) > 1 or array_keys($arguments)[0] !== 0) ? $arguments : last($arguments);
                } else {
                    $arg = null;
                }
                if ($policyWrp->call($callback_name, $this->user, $arg, $ability)) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

}
