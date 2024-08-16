<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
use Dlnsk\HierarchicalRBAC\Providers\EloquentPermissionProvider;
use Illuminate\Support\Facades\Gate;


class EloquentPermissionsTest extends TestCase
{
    protected $user;
    protected $post;

    public function setUp(): void
    {
        parent::setUp();
        app()->bind(PermissionsProvider::class, EloquentPermissionProvider::class);
        $this->user = (object)[];
        $this->post = new Post();
    }

    public function test_user_has_no_permission_attribute()
    {
        $this->user->roles = 'user';

        $this->assertFalse(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_has_excluded_permission()
    {
        $this->user->id = 1;
        $this->user->roles = 'manager';
        $this->user->permissions = collect([
            (object)[
                'user_id' => 1,
                'name' => 'deleteAnyPost',
                'action' => 'exclude',
            ],
        ]);

        $this->assertFalse(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_has_included_permission()
    {
        $this->user->id = 1;
        $this->user->roles = 'user';
        $this->user->permissions = collect([
            (object)[
                'user_id' => 1,
                'name' => 'deleteAnyPost',
                'action' => 'include',
            ],
        ]);

        $this->assertTrue(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_has_both_excluded_and_included_permission()
    {
        $this->user->id = 1;
        $this->user->roles = 'manager';
        $this->user->permissions = collect([
            (object)[
                'user_id' => 1,
                'name' => 'deleteAnyPost',
                'action' => 'exclude',
            ],
            (object)[
                'user_id' => 1,
                'name' => 'deleteAnyPost',
                'action' => 'include',
            ],
        ]);

        $this->assertFalse(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_can_edit_concrete_post()
    {
        $this->user->id = 1;
        $this->user->roles = 'user';
        $this->user->permissions = collect([
            (object)[
                'user_id' => 1,
                'name' => 'editFixedPost',
                'action' => 'include',
                'value' => 5,
            ],
        ]);
        $this->post->id = 5;
        $this->post->user_id = 999;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));


        $this->post->id = 22;

        $this->assertFalse(Gate::forUser($this->user)->allows('edit', $this->post));
    }

    public function test_user_can_edit_number_concrete_posts()
    {
        $this->user->id = 1;
        $this->user->roles = 'user';
        $this->user->permissions = collect([
            (object)[
                'user_id' => 1,
                'name' => 'editFixedPost',
                'action' => 'include',
                'value' => 5,
            ],
            (object)[
                'user_id' => 1,
                'name' => 'editFixedPost',
                'action' => 'include',
                'value' => 44,
            ],
        ]);
        $this->post->id = 5;
        $this->post->user_id = 999;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));


        $this->post->id = 44;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));
    }

}
