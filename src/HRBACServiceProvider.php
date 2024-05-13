<?php
namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionChecker;
use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
use Dlnsk\HierarchicalRBAC\Contracts\RolesProvider;
use Dlnsk\HierarchicalRBAC\Providers\ArrayPermissionProvider;
use Dlnsk\HierarchicalRBAC\Providers\EloquentRolesProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * Based on native Laravel's abilities. Hierarchical RBAC with callbacks.
 *
 * @author: Dmitry Pupinin
 */
class HRBACServiceProvider extends ServiceProvider {

    /**
     * This will be used to register config & view in
     * package namespace.
     */
    protected $packageName = 'h-rbac';

    public $bindings = [
        PermissionChecker::class => CommonPermissionChecker::class,
        RolesProvider::class => EloquentRolesProvider::class,
        PermissionsProvider::class => ArrayPermissionProvider::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            // Register your migration's publisher
            $this->publishes([
                __DIR__ . '/../database/migrations/add_role_field_to_users.stub'
                                => database_path('migrations/' . date('Y_m_d_His', time()) . '_add_role_field_to_users.php'),
            ], 'migrations');

            // Publish your config
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path($this->packageName.'.php'),
            ], 'config');

        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/config.php', $this->packageName);

        Gate::before(function ($user, $ability, $arguments) {
            $permissionChecker = resolve(PermissionChecker::class, compact('user'));
            return $permissionChecker->check($ability, $arguments);
        });

        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('role', function ($roles) {
                return '<?php
                    $__many_roles  = config("h-rbac.manyRolesAttribute");
                    $__single_role = config("h-rbac.singleRoleAttribute", "role");
                    $__user_roles = Arr::wrap(auth()->user()->$__single_role ?? null) ?: Arr::wrap(auth()->user()->$__many_roles ?? null);
                    if(auth()->check() && array_intersect($__user_roles, explode("|", '.$roles.'))): ?>';
            });
            $bladeCompiler->directive('endrole', function () {
                return '<?php endif; ?>';
            });
        });
    }

}
