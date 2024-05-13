<?php /** @noinspection NonAsciiCharacters */

namespace Dlnsk\HierarchicalRBAC\Tests;

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
        app()['config']->set('h-rbac.manyRolesAttribute', 'changedName');
        $this->user->own_roles = ['manager'];

        $this->assertFalse(Gate::forUser($this->user)->allows('delete', $this->post));


        $this->user->changedName = ['manager'];
        $this->assertTrue(Gate::forUser($this->user)->allows('delete', $this->post));
    }

}
