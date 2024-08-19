<?php
namespace Dlnsk\HierarchicalRBAC;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionChecker;
use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
use Dlnsk\HierarchicalRBAC\Contracts\RolesProvider;
use Dlnsk\HierarchicalRBAC\Providers\ArrayPermissionProvider;
use Dlnsk\HierarchicalRBAC\Providers\ArrayRolesProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        RolesProvider::class => ArrayRolesProvider::class,
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
                __DIR__ . '/../database/migrations/add_permissions_table.stub'
                                => database_path('migrations/' . date('Y_m_d_His', time()) . '_add_permissions_table.php'),
            ], 'hrbac-migrations');

            // Publish your config
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path($this->packageName.'.php'),
            ], 'hrbac-config');
            $this->publishes([
                __DIR__.'/../Policies/' => app_path('Policies'),
            ], 'hrbac-config');

        }

        Blade::if('role', function ($roles) {
            $rolesProvider = resolve(RolesProvider::class);
            $user_roles = $rolesProvider->getUserRoles(auth()->user());

            return auth()->check() && array_intersect($user_roles, explode("|", $roles));
        });
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
    }

}
