<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\Shomisha\UnusualRelationships\Test\Models\Performer::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
