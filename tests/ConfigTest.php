<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
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

}
