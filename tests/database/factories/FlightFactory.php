<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\Shomisha\UnusualRelationships\Test\Models\Flight::class, function (Faker $faker) {
    return [
        'flight_number' => $faker->randomAscii,
    ];
});
