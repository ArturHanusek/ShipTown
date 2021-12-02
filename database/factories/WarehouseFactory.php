<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderAddress;
use Faker\Generator as Faker;

$factory->define(\App\Models\Warehouse::class, function (Faker $faker) {
    $address_id = factory(OrderAddress::class)->create();

    return [
        'name'  => $faker->city,
        'code'  => rand(1, 1000),
        'address_id' => $address_id,
    ];
});
