<?php


use Dlnsk\HierarchicalRBAC\PolicyWrapper;

class PolicyTest extends \Dlnsk\HierarchicalRBAC\Tests\TestCase
{
    protected $wrapper;

    public function setUp(): void
    {
        parent::setUp();
        $policy = (object)[
            'chains' => [
                'a' => ['a1', 'a2', 'a3'],
                'b' => ['b1', 'b2'],
                'c',
            ]
        ];
        $this->wrapper = new PolicyWrapper($policy);
    }

    public function test_searching_ability_in_keys()
    {
        $this->assertTrue($this->wrapper->hasAbility('a'));
        $this->assertTrue($this->wrapper->hasAbility('b'));
        $this->assertTrue($this->wrapper->hasAbility('c'));
    }

    public function test_searching_ability_in_chains()
    {
        $this->assertTrue($this->wrapper->hasAbility('a1'));
        $this->assertTrue($this->wrapper->hasAbility('a2'));
        $this->assertTrue($this->wrapper->hasAbility('a3'));
        $this->assertTrue($this->wrapper->hasAbility('b2'));
    }

    public function test_no_policy()
    {
        $wrapper = new PolicyWrapper(null);

        $this->assertFalse($wrapper->isValid());
    }

    public function test_policy_with_no_chains()
    {
        $wrapper = new PolicyWrapper((object)[]);

        $this->assertFalse($wrapper->isValid());
    }

    public function test_valid_policy()
    {
        $this->assertTrue($this->wrapper->isValid());
    }

    public function test_taking_a_chain_by_key()
    {
        $chain = $this->wrapper->getChainFor('a');

        $this->assertSame(['a', 'a1', 'a2', 'a3'], $chain);
    }

    public function test_taking_a_chain_by_permission()
    {
        $chain = $this->wrapper->getChainFor('a1');

        $this->assertSame(['a1'], $chain);
    }
}
