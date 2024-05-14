<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Illuminate\Support\Facades\Gate;


class RolesTest extends TestCase
{
    protected $user;
    protected $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = (object)[];
        $this->post = new Post();
    }

    public function test_user_has_all_dummy_roles()
    {
        $this->user->own_roles = ['dummy1', 'dummy2'];

        $this->assertFalse(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_has_right_role_and_permission()
    {
        $this->user->own_roles = ['dummy1', 'manager'];

        $this->assertTrue(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_with_intersected_roles_edit_own_post()
    {
        $this->user->own_roles = ['user', 'manager'];
        $this->user->id = 1;
        $this->post->user_id = 1;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));
    }

    public function test_user_with_intersected_roles_edit_someone_else_post()
    {
        $this->user->own_roles = ['user', 'manager'];
        $this->user->id = 1;
        $this->post->user_id = 2;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));
    }

}
