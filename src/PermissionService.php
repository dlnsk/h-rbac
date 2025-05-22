<?php

namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
use Dlnsk\HierarchicalRBAC\Contracts\RolesProvider;
use Dlnsk\HierarchicalRBAC\Exceptions\PermissionNotFoundException;
use Dlnsk\HierarchicalRBAC\Exceptions\UserHasNoBuiltInRolesException;
use Facade\Ignition\Support\ComposerClassMap;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PermissionService
{
    public function getBuiltInPolicies(): Collection
    {
        $available_classes = array_keys((new ComposerClassMap)->listClasses());
        $policies = array_filter($available_classes, function($item) {
            return Str::contains($item, "\\Policies\\") && Str::endsWith($item, "Policy");
        });

        return collect($policies);
    }

    public function getBuiltInPermissions(): Collection
    {
        $policies = $this->getBuiltInPolicies();
        $permissions = collect();
        foreach ($policies as $policyClass) {
            $policy = new $policyClass();
            if (isset($policy->chains)) {
                $className = last(explode("\\", $policyClass));
                $permissions->put($className, $policy->chains);
            }
        }

        return $permissions;
    }

    public function getPolicy(array $arguments)
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

        return $policy;
    }

    public function getPolicyNameByPermission($permission_name)
    {
        $permissions = $this->getBuiltInPermissions();

        return $permissions
            ->map(function ($item, $key) use ($permission_name) {
                $permission_list = collect($item)->flatten();
                if ($permission_list->contains($permission_name)) {
                    return true;
                }
                return null;
            })
            ->filter()
            ->keys()
            ->first();
    }

    /**
     * @throws BindingResolutionException
     */
    public function getPolicyByPermission($permission_name): PolicyWrapper
    {
        $policy_name = $this->getPolicyNameByPermission($permission_name);
        $policy_FQN = $this->getBuiltInPolicies()
            ->first(function ($item) use ($policy_name) {
                return Str::endsWith($item, $policy_name);
            });
        $policy = app()->make($policy_FQN);

        return new PolicyWrapper($policy);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getPermissionParams($permission_name)
    {
        $policyWrp = $this->getPolicyByPermission($permission_name);
        $callback_name = $policyWrp->getCallbackName($permission_name, 'Params');
        if ($callback_name) {
            return $policyWrp->simpleCall($callback_name);
        }

        return null;
    }

    /**
     * @throws UserHasNoBuiltInRolesException
     */
    public function getUserRoles($user): array
    {
        $rolesProvider = resolve(RolesProvider::class);
        $user_roles = $rolesProvider->getUserRoles($user);
        $application_roles = $rolesProvider->getApplicationRoles();
        $intersection = array_intersect($application_roles, $user_roles);
        if (!count($intersection)) {
            throw new UserHasNoBuiltInRolesException();
        }

        return $intersection;
    }

    public function getUserPermissions($user): Collection
    {
        $permissionsProvider = resolve(PermissionsProvider::class, compact('user'));
        $user_roles = $this->getUserRoles($user);

        return $permissionsProvider->getPermissions($user_roles);
    }

    /**
     * return Collection
     * @throws PermissionNotFoundException
     * @throws UserHasNoBuiltInRolesException
     */
    public function getUserPermissionsOfAbility($user, $ability, $policyWrapper): Collection
    {
        if (!$policyWrapper->isValid() || !$policyWrapper->hasAbility($ability)) {
            throw new PermissionNotFoundException();
        }
        $chain = $policyWrapper->getChainFor($ability);
        $user_permissions = $this->getUserPermissions($user);

        return $user_permissions->intersectByKeys(array_fill_keys($chain, null));
    }

    public function getRolesPermissions($roles): Collection
    {
        $rolesProvider = resolve(RolesProvider::class);

        return $rolesProvider->getRolesPermissions($roles);
    }

    public function getUserExtraPermissions($user): Collection
    {
        $permissionsProvider = resolve(PermissionsProvider::class, compact('user'));

        return $permissionsProvider->getExtraPermissions();
    }
}
