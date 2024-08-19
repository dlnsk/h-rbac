<?php

namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
use Dlnsk\HierarchicalRBAC\Contracts\RolesProvider;
use Facade\Ignition\Support\ComposerClassMap;
use Illuminate\Support\Collection;
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

    public function getUserRoles($user): array
    {
        $rolesProvider = resolve(RolesProvider::class, compact('user'));
        $user_roles = $rolesProvider->getUserRoles();
        $application_roles = $rolesProvider->getApplicationRoles();

        return array_intersect($application_roles, $user_roles);
    }

    public function getUserPermissions($user): Collection
    {
        $permissionsProvider = resolve(PermissionsProvider::class, compact('user'));
        $user_roles = $this->getUserRoles($user);

        return $permissionsProvider->getPermissions($user_roles);
    }
}
