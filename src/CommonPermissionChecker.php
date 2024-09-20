<?php

namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
use Dlnsk\HierarchicalRBAC\Contracts\RolesProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CommonPermissionChecker implements Contracts\PermissionChecker
{
    /**
     * @var PermissionService
     */
    private $permissionService;
    /**
     * @var PermissionsProvider
     */
    private $permissionsProvider;
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
        $this->permissionService = resolve(PermissionService::class);
        $this->permissionsProvider = resolve(PermissionsProvider::class, compact('user'));
    }

    /**
     * Make some preparation for checking ability.
     *
     * @param string $ability   The head ability whose chain we are checking
     * @param mixed $arguments  Additional arguments for checking (model, policy and any other data)
     * @return bool|null
     * @throws BindingResolutionException
     */
    public function check($ability, $arguments): ?bool
    {
        $user_roles = $this->permissionService->getUserRoles($this->user);
        if (!count($user_roles)) {
            // User has no application's built-in roles
            return null;
        }
        $user_permissions = $this->permissionsProvider->getPermissions($user_roles);

        return $this->checkAbility($user_permissions, $ability, $arguments);
    }

    /**
     * Checking permission for chose user
     *
     * @param Collection $user_permissions  Set of permissions that user has. Structure is [<permission_name> => <collection of permissions> or NULL, ...]
     * @param string $ability               The head ability whose chain we are checking
     * @param mixed $arguments              Additional arguments for checking (model, policy and any other data)
     * @return bool|null
     * @throws BindingResolutionException
     */
    public function checkAbility($user_permissions, $ability, $arguments): ?bool
    {
        if (isset($arguments['policy'])) {
            $arg1 = $arguments['policy'];
        } else {
            $arg1 = head($arguments);
        }
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
        $permission_intersection = $user_permissions->intersectByKeys(array_fill_keys($chain, null));
        foreach ($permission_intersection as $permission => $values) {
            $callback_name = $policyWrp->getCallbackName($permission);
            if ($callback_name) {
                if (!empty($arguments)) {
                    // Pass arguments as array if it has more than one element, or it's associative array
                    $arg = (count($arguments) > 1 or array_keys($arguments)[0] !== 0) ? $arguments : last($arguments);
                } else {
                    $arg = null;
                }
                if ($policyWrp->call($callback_name, $this->user, $arg, $values, $ability)) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

}
