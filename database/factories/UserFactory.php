<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\File;
use App\Folder;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

/*$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => 'admin',
        'last_name' => 'edm',
        'gender' => 'm',
        'email' => 'admin.edm@edm.com',
        'password' => bcrypt('secret'),
        'created_at' => now()->format('Y-m-d H:i:s'),
        'updated_at' => now()->format('Y-m-d H:i:s')
    ];
});*/

$factory->define(Folder::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'name' => $faker->name,
        'is_folder' => true,
    ];
});

$factory->define(File::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'name' => $faker->name,
        'is_folder' => false,
    ];
});
