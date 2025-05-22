<?php

namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PolicyBuilder;
use Dlnsk\HierarchicalRBAC\Exceptions\PermissionNotFoundException;
use Dlnsk\HierarchicalRBAC\Exceptions\UserHasNoBuiltInRolesException;

class CommonPermissionChecker implements Contracts\PermissionChecker
{
    /**
     * @var PermissionService
     */
    private $permissionService;
    /**
     * @var PolicyBuilder
     */
    private $policyBuilder;
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
        $this->permissionService = resolve(PermissionService::class);
    }

    public function setPolicyBuilder(PolicyBuilder $builder)
    {
        $this->policyBuilder = $builder;
    }

    /**
     * Checking permission for chosen user
     *
     * @param string $ability The head ability whose chain we are checking
     * @param mixed $arguments Additional arguments for checking (model, policy and any other data)
     * @return bool|null
     * @throws PermissionNotFoundException
     */
    public function check($ability, $arguments): ?bool
    {
        $policy = $this->permissionService->getPolicy($arguments);
        $policyWrapper = $this->policyBuilder->createWrapper($policy);

        try {
            $user_permissions_of_ability = $this->permissionService
                ->getUserPermissionsOfAbility(
                    $this->user,
                    $ability,
                    $policyWrapper
                );
        } catch (UserHasNoBuiltInRolesException $e) {
            return null;
        } catch (PermissionNotFoundException $e) {
            if (config('h-rbac.exceptIfPermissionNotFound', false)) {
                throw $e;
            } else {
                return null;
            }
        }

        foreach ($user_permissions_of_ability as $permission => $values) {
            $callback_name = $policyWrapper->getCallbackName($permission);
            if ($callback_name) {
                if (!empty($arguments)) {
                    // Pass arguments as array if it has more than one element, or it's associative array
                    $arg = (count($arguments) > 1 or array_keys($arguments)[0] !== 0) ? $arguments : last($arguments);
                } else {
                    $arg = null;
                }
                if ($policyWrapper->call($callback_name, $this->user, $arg, $values, $ability)) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

}
