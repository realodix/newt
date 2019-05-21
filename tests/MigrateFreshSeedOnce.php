<?php

namespace Tests;

use App\User;
use Illuminate\Support\Facades\Artisan;

trait MigrateFreshSeedOnce
{
    /**
     * If true, setup has run at least once.
     *
     * @var bool
     */
    protected static $setUpHasRunOnce = false;

    /**
     * After the first run of setUp "migrate:fresh --seed".
     *
     * @return void
     */
    public function setUp():void
    {
        parent::setUp();

        if (! static::$setUpHasRunOnce) {
            Artisan::call('migrate:fresh');
            Artisan::call(
                'db:seed', ['--class' => 'DatabaseSeeder']
            );

            static::$setUpHasRunOnce = true;
        }
    }

    protected function admin()
    {
        return User::whereName('admin')->first();
    }

    protected function user()
    {
        return User::whereName('admin')->first();
    }

    protected function adminPassword()
    {
        return 'admin';
    }

    protected function userPassword()
    {
        return 'user';
    }

    protected function loginAsAdmin()
    {
        $this->actingAs($this->admin());

        return $admin;
    }

    protected function loginAsUser()
    {
        $this->actingAs($this->user());

        return $user;
    }
}
