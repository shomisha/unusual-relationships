<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\Shomisha\UnusualRelationships\Test\Models\Member::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'performer_id' => function() {
            return factory(\Shomisha\UnusualRelationships\Test\Models\Performer::class)->create()->id;
        },
    ];
});
