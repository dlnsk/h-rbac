<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Dlnsk\HierarchicalRBAC\HRBACServiceProvider;


abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();
        app()['config']->set('h-rbac.rbacClass', TestAuthorizationClass::class);
    }


    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            HRBACServiceProvider::class,
        ];
    }

}
