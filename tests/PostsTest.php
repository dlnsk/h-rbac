<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Dlnsk\HierarchicalRBAC\HRBACHelper;
use Dlnsk\HierarchicalRBAC\Tests\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;


class PostsTest extends TestCase
{
    protected $user;
    protected $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = (object)[];
        $this->post = new Post();
    }

    public function test_user_has_no_any_roles()
    {
        $this->assertFalse(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_has_dummy_role()
    {
        $this->user->roles = 'dummy';

        $this->assertFalse(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_has_right_role_an_permission()
    {
        $this->user->roles = 'manager';

        $this->assertTrue(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_has_right_role_but_no_permission()
    {
        $this->user->roles = 'manager';

        $this->assertFalse(Gate::forUser($this->user)->allows('dummy_permission', $this->post));
    }

    public function test_manager_edit_own_post()
    {
        $this->user->roles = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 1;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));
    }

    public function test_manager_edit_someone_else_post()
    {
        $this->user->roles = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 2;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));
    }

    public function test_user_edit_own_post()
    {
        $this->user->roles = 'user';
        $this->user->id = 1;
        $this->post->user_id = 1;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));
    }

    public function test_checking_concrete_allowed_permission()
    {
        $this->user->roles = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 1;

        // Permission has no callback
        $this->assertTrue(Gate::forUser($this->user)->allows('editAnyPost', $this->post));
    }

    public function test_checking_concrete_disallowed_permission()
    {
        $this->user->roles = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 1;

        // Manager can edit any posts but he don't have this concrete permission
        $this->assertFalse(Gate::forUser($this->user)->allows('editOwnPost', $this->post));
    }

    public function test_checking_concrete_allowed_permission_with_callback()
    {
        $this->user->roles = 'user';
        $this->user->id = 1;
        $this->post->user_id = 1;

        // Permission has a callback
        $this->assertTrue(Gate::forUser($this->user)->allows('editOwnPost', $this->post));
    }

    public function test_checking_concrete_disallowed_permission_without_callback()
    {
        $this->user->roles = 'user';
        $this->user->id = 1;
        $this->post->user_id = 1;

        // User don't have a permission
        $this->assertFalse(Gate::forUser($this->user)->allows('editAnyPost', $this->post));
    }

    public function test_user_edit_someone_else_post()
    {
        $this->user->roles = 'user';
        $this->user->id = 1;
        $this->post->user_id = 2;

        $this->assertFalse(Gate::forUser($this->user)->allows('edit', $this->post));
    }

    public function test_user_has_any_permission_in_ability()
    {
        $this->user->roles = 'user';
        $this->user->id = 1;
        $this->post->user_id = 2;

        $hrbacService = resolve(HRBACHelper::class);

        $this->assertTrue($hrbacService->canUserTakeAbility($this->user, 'edit', PostPolicy::class));
    }

    public function test_user_has_no_permission_in_ability()
    {
        $this->user->roles = 'user';
        $this->user->id = 1;

        $hrbacService = resolve(HRBACHelper::class);

        $this->assertFalse($hrbacService->canUserTakeAbility($this->user, 'delete', PostPolicy::class));
    }

}
