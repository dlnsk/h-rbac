<?php

namespace Dlnsk\HierarchicalRBAC;

use Illuminate\Support\Str;

class PolicyWrapper
{
    private $policy;

    public function __construct($policy)
    {
        $this->policy = $policy;
    }

    public function hasAbility($ability): bool
    {
        return collect($this->policy->chains)
            ->flatten()
            ->merge(array_keys($this->policy->chains))
            ->contains($ability);
    }

    public function getCallbackName($permission)
    {
        $methods = get_class_methods($this->policy);
        $callback_name = Str::camel($permission);
        return in_array($callback_name, $methods) ? $callback_name : false;
    }

    public function call($callback_name, $user, $arg, $permission_value, $ability): bool
    {
        return $this->policy->$callback_name($user, $arg, $permission_value, $ability);
    }

    public function isValid(): bool
    {
        return !is_null($this->policy) && isset($this->policy->chains);
    }

    public function getChainFor($ability): array
    {
        if (in_array($ability, $this->policy->chains)) {
            return [$ability];
        } elseif (isset($this->policy->chains[$ability])) {
            return array_unique(array_merge([$ability], $this->policy->chains[$ability]));
        }
        // Try to find the ability among the permissions. This is a rare case to check permission inside a chain.
        foreach ($this->policy->chains as $chain) {
            if (in_array($ability, $chain)) {
                return [$ability];
            }
        }

        return [];
    }
}
