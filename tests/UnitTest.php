<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Gate;

class UnitTest extends TestCase
{
    protected $user;
    protected $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = (object)[];
        $this->post = (object)[];
    }

    /** @test */
    public function у_пользователя_нет_ничего_связанного_с_ролями()
    {
        $this->assertFalse(Gate::forUser($this->user)->allows('deletePost'));
    }

    /** @test */
    public function у_пользователя_ошибочная_роль()
    {
        $this->user->role = 'dummy';

        $this->assertFalse(Gate::forUser($this->user)->allows('deletePost'));
    }

    /** @test */
    public function у_пользователя_правильная_роль_и_есть_право()
    {
        $this->user->role = 'manager';

        $this->assertTrue(Gate::forUser($this->user)->allows('deletePost'));
    }

    /** @test */
    public function у_пользователя_правильная_роль_но_права_нет()
    {
        $this->user->role = 'manager';

        $this->assertFalse(Gate::forUser($this->user)->allows('dummy_permission'));
    }

    /** @test */
    public function менеджер_редактирует_свой_пост()
    {
        $this->user->role = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 1;

        $this->assertTrue(Gate::forUser($this->user)->allows('editPost', $this->post));
    }

    /** @test */
    public function менеджер_редактирует_чужой_пост()
    {
        $this->user->role = 'manager';
        $this->user->id = 1;
        $this->post->user_id = 2;

        $this->assertTrue(Gate::forUser($this->user)->allows('editPost', $this->post));
    }

    /** @test */
    public function пользователь_редактирует_свой_пост()
    {
        $this->user->role = 'user';
        $this->user->id = 1;
        $this->post->user_id = 1;

        $this->assertTrue(Gate::forUser($this->user)->allows('editPost', $this->post));
    }

    /** @test */
    public function пользователь_редактирует_чужой_пост()
    {
        $this->user->role = 'user';
        $this->user->id = 1;
        $this->post->user_id = 2;

        $this->assertFalse(Gate::forUser($this->user)->allows('editPost', $this->post));
    }


/////////////////////////////////////////


    /** @test */
    public function у_пользователя_все_роли_ошибочные()
    {
        $this->user->own_roles = ['dummy1', 'dummy2'];

        $this->assertFalse(Gate::forUser($this->user)->allows('deletePost'));
    }

    /** @test */
    public function у_пользователя_есть_правильная_роль_и_право()
    {
        $this->user->own_roles = ['dummy1', 'manager'];

        $this->assertTrue(Gate::forUser($this->user)->allows('deletePost'));
    }

    /** @test */
    public function пользователь_с_пересекающимися_правами_редактирует_свой_пост()
    {
        $this->user->own_roles = ['user', 'manager'];
        $this->user->id = 1;
        $this->post->user_id = 1;

        $this->assertTrue(Gate::forUser($this->user)->allows('editPost', $this->post));
    }

    /** @test */
    public function пользователь_с_пересекающимися_правами_редактирует_чужой_пост()
    {
        $this->user->own_roles = ['user', 'manager'];
        $this->user->id = 1;
        $this->post->user_id = 2;

        $this->assertTrue(Gate::forUser($this->user)->allows('editPost', $this->post));
    }


///////////////////////////////////


    /** @test */
    public function изменение_имени_атрибута_возвращающего_несколько_ролей()
    {
        app()['config']->set('h-rbac.userRolesAttribute', 'changedName');
        $this->user->own_roles = ['manager'];

        $this->assertFalse(Gate::forUser($this->user)->allows('deletePost'));


        $this->user->changedName = ['manager'];
        $this->assertTrue(Gate::forUser($this->user)->allows('deletePost'));
    }

}
