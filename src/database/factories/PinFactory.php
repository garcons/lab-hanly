<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Eloquents\Pin;
use Faker\Generator as Faker;

$factory->define(Pin::class, function (Faker $faker) {
    return [
        'friends_id' => factory(\App\Eloquents\Friend::class)->create()->id,
        'latitude' => $faker->latitude($min = 20, $max = 45),
        'longitude' => $faker->longitude($min = 122, $max = 153),
    ];
});
