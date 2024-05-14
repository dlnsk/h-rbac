<?php

namespace Dlnsk\HierarchicalRBAC\Tests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

class ViewsTest extends TestCase
{
    protected $template = '
        general
        @role("dummy")
            has_dummy
        @endrole
        @role("user")
            has_user_role
        @endrole
        @role("manager")
            has_manager_role
        @endrole
        @role("manager|user")
            has_or_role_1
        @endrole
        @role("user|manager")
            has_or_role_2
        @endrole
    ';

    /**
     * Compile blade template from string
     *
     * @param  string $value string that contains blade template
     * @param  array  $args  set of variables used in blade
     * @return string        compiled blade template
     */
    public function bladeCompile($value, array $args = array())
    {
        $generated = Blade::compileString($value);

        ob_start() and extract($args, EXTR_SKIP);

        // We'll include the view contents for parsing within a catcher
        // so we can avoid any WSOD errors. If an exception occurs we
        // will throw it out to the exception handler.
        try
        {
            eval('?>'.$generated);
        }

        // If we caught an exception, we'll silently flush the output
        // buffer so that no partially rendered views get thrown out
        // to the client and confuse the user with junk.
        catch (\Exception $e)
        {
            ob_get_clean(); throw $e;
        }

        $content = ob_get_clean();

        return $content;
    }


    public function test_blade_role_directive()
    {
        $user = (object)[];
        $user->roles = 'manager';
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);


        $this->assertStringContainsString('general', $this->bladeCompile($this->template));
        $this->assertStringNotContainsString('has_dummy', $this->bladeCompile($this->template));
        $this->assertStringNotContainsString('has_user_role', $this->bladeCompile($this->template));
        $this->assertStringContainsString('has_manager_role', $this->bladeCompile($this->template));
        $this->assertStringContainsString('has_or_role_1', $this->bladeCompile($this->template));
        $this->assertStringContainsString('has_or_role_2', $this->bladeCompile($this->template));
    }

}
