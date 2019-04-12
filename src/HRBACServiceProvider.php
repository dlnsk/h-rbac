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
        // Register your migration's publisher
        $this->publishes([
            __DIR__.'/../database/migrations/' => base_path('database/migrations')
        ], 'migrations');
        
        // Publish your config
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path($this->packageName.'.php'),
            __DIR__.'/../config/exampleClass.php' => app_path('Classes/Authorization/AuthorizationClass.php'),
        ], 'config');

        //
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
                return "<?php if(auth()->check() && in_array(auth()->user()->role, explode('|', $roles))): ?>";
            });
            $bladeCompiler->directive('endrole', function () {
                return '<?php endif; ?>';
            });
        });
    }

}
