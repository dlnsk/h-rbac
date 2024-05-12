<?php /** @noinspection NonAsciiCharacters */

namespace Dlnsk\HierarchicalRBAC\Tests;

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
        $this->user->role = 'dummy';

        $this->assertFalse(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_has_right_role_an_permission()
    {
        $this->user->role = 'manager';

        $this->assertTrue(Gate::forUser($this->user)->allows('delete', $this->post));
    }

    public function test_user_has_right_role_but_no_permission()
    {
        $this->user->role = 'manager';

        $this->assertFalse(Gate::forUser($this->user)->allows('dummy_permission', $this->post));
    }

    public function test_manager_edit_own_post()
    {
        $this->user->role = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 1;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));
    }

    public function test_manager_edit_someone_else_post()
    {
        $this->user->role = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 2;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));
    }

    public function test_user_edit_own_post()
    {
        $this->user->role = 'user';
        $this->user->id = 1;
        $this->post->user_id = 1;

        $this->assertTrue(Gate::forUser($this->user)->allows('edit', $this->post));
    }

    public function test_user_edit_someone_else_post()
    {
        $this->user->role = 'user';
        $this->user->id = 1;
        $this->post->user_id = 2;

        $this->assertFalse(Gate::forUser($this->user)->allows('edit', $this->post));
    }

}
