<?php
namespace Dlnsk\HierarchicalRBAC\Backend\Http;

use Dlnsk\HierarchicalRBAC\Backend\Models\Permission;
use Dlnsk\HierarchicalRBAC\Backend\Policies\PermissionPolicy;
use Dlnsk\HierarchicalRBAC\PermissionService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Rule;

class PermissionController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Show the list of user's permissions.
     *
     * @param PermissionService $permissionService
     * @param string $user
     * @return View
     */
    public function index(PermissionService $permissionService, string $user)
    {
        $this->authorize('managePermissions', PermissionPolicy::class);
        $userClass = config('auth.providers.users.model');
        $user = $userClass::query()->findOrFail($user);

        $user_roles = $user->roles;
        $available_permissions = $permissionService->getBuiltInPermissions();
        $roles_permissions = $permissionService->getRolesPermissions($user_roles);
        $extra_permissions = $permissionService->getUserExtraPermissions($user)
            ->groupBy(['name', 'action'])
            ->mapWithKeys(function ($item, $key) {
                $groups = $item->keys();
                $include = $groups->contains(Permission::INCLUDE);
                $exclude = $groups->contains(Permission::EXCLUDE);

                return [$key => (object)[
                    'include' => $include,
                    'exclude' => $exclude,
                    'warning' => $include && $exclude,
                    'values' => optional($item->get(Permission::INCLUDE))->pluck('value'),
                ]];

            });

        return view('h-rbac::permission.list', compact([
            'user',
            'user_roles',
            'available_permissions',
            'roles_permissions',
            'extra_permissions',
        ]));
    }

    /**
     * Store the new overrided permission.
     *
     * @param Request $request
     * @param string $user
     * @return RedirectResponse
     */
    public function store(Request $request, string $user)
    {
        $this->authorize('managePermissions', PermissionPolicy::class);
        $userClass = config('auth.providers.users.model');
        $user = $userClass::query()->findOrFail($user);

        $this->validate($request, [
            'action' => [
                'required',
                Rule::in([
                    Permission::INCLUDE,
                    Permission::EXCLUDE,
                ])
            ],
        ]);

        $data = $request->all();
        if ($data['action'] === Permission::EXCLUDE) {
            unset($data['value']);
        }
        $model = new Permission($data);
        $user->permissions()->save($model);

        return redirect()->route('permissions.edit', [
            'user' => $user,
            'permission' => $data['name'],
        ]);
    }

    /**
     * Show a form to override the permission.
     *
     * @param PermissionService $permissionService
     * @param string $user
     * @param string $permission_name
     * @return View
     * @throws BindingResolutionException
     */
    public function edit(PermissionService $permissionService, string $user, string $permission_name)
    {
        $this->authorize('managePermissions', PermissionPolicy::class);
        $userClass = config('auth.providers.users.model');
        $user = $userClass::query()->findOrFail($user);

        $permissions = Permission::query()
            ->where('user_id', $user->id)
            ->where('name', $permission_name)
            ->orderBy('action')
            ->orderBy('id')
            ->get();
        $policy_name = $permissionService->getPolicyNameByPermission($permission_name);
        $params = $permissionService->getPermissionParams($permission_name);

        return view('h-rbac::permission.edit', compact([
            'user',
            'permission_name',
            'permissions',
            'policy_name',
            'params',
        ]));
    }

    /**
     * Delete the permission's overrides.
     *
     * @param string $user
     * @param string $permission
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(string $user, string $permission)
    {
        $this->authorize('managePermissions', PermissionPolicy::class);
        $permission = Permission::query()->findOrFail($permission);

        $permission_name = $permission->name;
        $permission->delete();

        return redirect()->route('permissions.edit', [
            'user' => $user,
            'permission' => $permission_name,
        ]);
    }
}
