<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Dlnsk\HierarchicalRBAC\Contracts\PermissionsProvider;
use Dlnsk\HierarchicalRBAC\HRBACHelper;
use Dlnsk\HierarchicalRBAC\Providers\EloquentPermissionProvider;
use Dlnsk\HierarchicalRBAC\Tests\Policies\PostPolicy;
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

    public function test_user_has_edit_concrete_permission_but_without_value_in_db() {
        app()['config']->set('h-rbac.builtinRoles', [
            'mentor' => [
                'editFixedPost',
            ],
        ]);

        $this->user->id = 1;
        $this->user->roles = 'mentor';
        $this->user->permissions = collect(); // No value in db to allow to edit post with id=5
        $this->post->id = 5;
        $this->post->user_id = 999;

        $this->assertFalse(Gate::forUser($this->user)->allows('edit', $this->post));
    }

    public function test_user_has_edit_concrete_permission_and_value_in_db() {
        app()['config']->set('h-rbac.builtinRoles', [
            'mentor' => [
                'editFixedPost',
            ],
        ]);

        $this->user->id = 1;
        $this->user->roles = 'mentor';
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
    }

    // Here the user has no permission in role, but it's included through the db record.
    // In general, you don't have to add permission like this in roles, because in some (very rare) sorts
    // it may prevent users who have "more wide" rights in other roles they have.
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

    public function test_user_gets_role_permission_by_helper()
    {
        $this->user->id = 1;
        $this->user->roles = 'user';

        $hrbac_helper = resolve(HRBACHelper::class);
        $permissions = $hrbac_helper->getPermissionsPayload($this->user, 'edit', PostPolicy::class);

        $this->assertCount(1, $permissions);
        $this->assertContainsEquals('editOwnPost', $permissions->keys());
    }

    public function test_user_gets_role_permission_but_without_excluded_by_helper()
    {
        $this->user->id = 1;
        $this->user->roles = 'user';
        $this->user->permissions = collect([
            (object)[
                'user_id' => 1,
                'name' => 'editOwnPost',
                'action' => 'exclude',
            ],
        ]);

        $hrbac_helper = resolve(HRBACHelper::class);
        $permissions = $hrbac_helper->getPermissionsPayload($this->user, 'edit', PostPolicy::class);

        $this->assertCount(0, $permissions);
    }

    public function test_user_gets_different_permission_by_helper()
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

        $hrbac_helper = resolve(HRBACHelper::class);
        $permissions = $hrbac_helper->getPermissionsPayload($this->user, 'edit', PostPolicy::class);

        $this->assertCount(2, $permissions);
        $this->assertContainsEquals('editOwnPost', $permissions->keys());
        $this->assertContainsEquals('editFixedPost', $permissions->keys());
    }

    public function test_user_gets_permission_values_by_helper()
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

        $hrbac_helper = resolve(HRBACHelper::class);
        $permissions = $hrbac_helper->getPermissionsPayload($this->user, 'edit', PostPolicy::class);

        $this->assertSame([5, 44], $permissions->get('editFixedPost')->pluck('value')->toArray());
    }

    public function test_user_gets_permission_without_excluded_by_helper()
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
                'action' => 'exclude',
            ],
        ]);

        $hrbac_helper = resolve(HRBACHelper::class);
        $permissions = $hrbac_helper->getPermissionsPayload($this->user, 'edit', PostPolicy::class);

        $this->assertCount(1, $permissions);
        $this->assertContainsEquals('editOwnPost', $permissions->keys());
    }
}
