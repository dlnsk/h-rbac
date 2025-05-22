<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
use Dlnsk\HierarchicalRBAC\Exceptions\PermissionNotFoundException;
use Dlnsk\HierarchicalRBAC\Providers\EloquentPermissionProvider;
use Illuminate\Support\Facades\Gate;


class ConfigTest extends TestCase
{
    protected $user;
    protected $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = (object)[];
        $this->post = new Post();
    }

    public function test_new_name_for_many_roles_attribute()
    {
        app()['config']->set('h-rbac.userRolesAttribute', 'changedName');
        $this->user->roles = ['manager'];

        $this->assertFalse(Gate::forUser($this->user)->allows('delete', $this->post));


        $this->user->changedName = ['manager'];
        $this->assertTrue(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_new_name_for_permissions_attribute()
    {
        app()->bind(PermissionsProvider::class, EloquentPermissionProvider::class);
        app()['config']->set('h-rbac.permissionsAttribute', 'changedName');
        $this->user->id = 1;
        $this->user->roles = 'user';
        $this->user->permissions = collect([
            (object)[
                'user_id' => 1,
                'name' => 'deleteAnyPost',
                'action' => 'include',
            ],
        ]);

        $this->assertFalse(Gate::forUser($this->user)->allows('delete', $this->post));


        $this->user->changedName = collect([
            (object)[
                'user_id' => 1,
                'name' => 'deleteAnyPost',
                'action' => 'include',
            ],
        ]);
        $this->assertTrue(Gate::forUser($this->user)->allows('delete', $this->post));
    }


    public function test_rise_exception_on_unknown_permission()
    {
        app()['config']->set('h-rbac.riseExceptionIfPermissionNotFound', true);
        $this->expectException(PermissionNotFoundException::class);

        $this->user->roles = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 1;

        $this->assertFalse(Gate::forUser($this->user)->allows('dummy', $this->post));
    }


    public function test_rise_exception_on_lost_permission()
    {
        app()['config']->set('h-rbac.riseExceptionIfPermissionNotFound', true);
        $this->expectException(PermissionNotFoundException::class);

        $this->user->roles = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 1;

        // Didn't point to Model or Policy where permission is.
        $this->assertTrue(Gate::forUser($this->user)->allows('editAnyPost'));
    }

    public function test_dont_rise_exception_on_unhandled_permission()
    {
        app()['config']->set('h-rbac.riseExceptionIfPermissionNotFound', false);

        $this->user->roles = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 1;

        $this->assertFalse(Gate::forUser($this->user)->allows('dummy', $this->post));
        $this->assertFalse(Gate::forUser($this->user)->allows('editAnyPost'));
    }
}
