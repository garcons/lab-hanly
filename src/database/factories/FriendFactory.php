<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Eloquents\Friend;
use Faker\Generator as Faker;

$factory->define(Friend::class, function (Faker $faker) {
    return [
        'nickname' => $faker->name($gender = null),
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('password'),
        'image_path' => null,
        'remember_token' => \Str::random(10),
    ];
});
