<?php

namespace Dlnsk\HierarchicalRBAC;

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
}