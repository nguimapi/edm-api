<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);

        DB::table('users')->insert(array(
                'name' => 'admin',
                'last_name' => 'edm',
                'gender' => 'm',
                'email' => 'admin.edm@edm.com',
                'password' => bcrypt('secret'),
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s')
            )
        );
    }
}
