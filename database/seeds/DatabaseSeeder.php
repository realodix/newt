<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);

        // Multiple with factory
        // factory(App\Models\User::class, 200)->create();
        // factory(App\Models\Url::class, 100000)->create();
    }
}
