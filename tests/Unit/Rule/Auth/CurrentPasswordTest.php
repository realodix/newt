<?php

namespace Tests\Unit\Rule\Auth;

use App\Rules\Auth\CurrentPassword;
use Tests\TestCase;

class CurrentPasswordTest extends TestCase
{
    protected $rule;

    public function setUp():void
    {
        parent::setUp();

        $this->rule = new CurrentPassword();
        $this->loginAsAdmin();
    }

    /**
     * @group u-rule
     */
    public function testCurrentPasswordPass()
    {
        $this->assertTrue($this->rule->passes('test', $this->adminPassword()));
    }

    /**
     * @group u-rule
     */
    public function testCurrentPasswordFail()
    {
        $this->assertFalse($this->rule->passes('test', 'wrong_password'));
    }
}
