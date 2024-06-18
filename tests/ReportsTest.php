<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Dlnsk\HierarchicalRBAC\Tests\Policies\ReportPolicy;
use Illuminate\Support\Facades\Gate;


class ReportsTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = (object)[];
    }

    public function test_user_has_permission_with_no_model()
    {
        $this->user->roles = 'manager';

        $this->assertTrue(Gate::forUser($this->user)->allows('list', ReportPolicy::class));
    }

    public function test_user_has_permission_with_no_model_and_policy_class_as_first_value()
    {
        $this->user->roles = 'manager';

        $this->assertTrue(Gate::forUser($this->user)->allows('list', [ReportPolicy::class, 'something' => 'dummy']));
    }

    public function test_user_has_permission_with_no_model_and_policy_class_with_key()
    {
        $this->user->roles = 'manager';

        $this->assertTrue(Gate::forUser($this->user)->allows('list', ['something' => 'dummy', 'policy' => ReportPolicy::class]));
    }

    public function test_user_has_permission_with_no_model_and_callback()
    {
        $this->user->roles = 'user';

        $this->assertTrue(Gate::forUser($this->user)->allows('list', [ReportPolicy::class, 'kind' => 'edu']));
    }

}
