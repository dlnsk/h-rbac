<?php
namespace Dlnsk\HierarchicalRBAC\Backend\Http;

use App\User;
use Dlnsk\HierarchicalRBAC\PermissionService;
use Dlnsk\HierarchicalRBAC\Backend\Models\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PermissionController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Просмотр списка разрешений пользователя.
     *
     * @param PassportService $passportService
     * @param PermissionService $permissionService
     * @param User $user
     * @return View|AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(PermissionService $permissionService, $user)
    {
        $userClass = config('auth.providers.users.model');
        $user = $userClass::query()->findOrFail($user);
//        $this->authorize('managePermissions', PermissionPolicy::class);


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
     * Добавление нового разрешения.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function store(Request $request, string $user)
    {
//        $this->authorize('managePermissions', PermissionPolicy::class);
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
     * Редактирование переопределенных разрешений.
     *
     * @param PermissionService $permissionService
     * @param User $user
     * @param string $permission_name
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function edit(PermissionService $permissionService, string $user, string $permission_name)
    {
//        $this->authorize('managePermissions', PermissionPolicy::class);
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
     * Удаление разрешения пользователя.
     *
     * @param User $user
     * @param Permission $permission
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(string $user, string $permission)
    {
//        $this->authorize('managePermissions', PermissionPolicy::class);
        $permission = Permission::query()->findOrFail($permission);

        $permission_name = $permission->name;
        $permission->delete();

        return redirect()->route('permissions.edit', [
            'user' => $user,
            'permission' => $permission_name,
        ]);
    }
}
