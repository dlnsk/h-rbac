<?php
namespace Dlnsk\HierarchicalRBAC;

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
                __DIR__.'/../classes/AuthorizationClass.php' => app_path('Classes/Authorization/AuthorizationClass.php'),
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

        \Gate::before(function ($user, $ability, $arguments) {
            $class = config($this->packageName.'.rbacClass');
            $rbac = new $class();
            return $rbac->checkPermission($user, $ability, $arguments);
        });

        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('role', function ($roles) {
                return '<?php
                    $__many_roles  = config("h-rbac.userRolesAttribute");
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
