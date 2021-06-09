<?php

/** @var Factory $factory */

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatus;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Order::class, function (Faker $faker) {
    $shippingAddress = OrderAddress::query()->inRandomOrder()->first() ?? factory(OrderAddress::class)->create();

    $statusList = ['processing',
        'on_hold', 'paid', 'picking',
        'packing', 'packing_warehouse', 'packing_web'
    ];

    $newOrder = [
        'order_number' => (string)(10000000 + $faker->unique()->randomNumber(7)),
        'total' => $faker->randomNumber(2),
        'shipping_address_id' => $shippingAddress->getKey(),
        'order_placed_at' => $faker->dateTimeBetween('-7days', now()),
        'status_code' => $faker->randomElement($statusList)
    ];

    if (OrderStatus::isComplete($newOrder['status_code'])) {
        $user = User::query()->inRandomOrder()->first('id') ?? factory(User::class)->create();

        $newOrder['order_closed_at'] = $faker->dateTimeBetween($newOrder['order_placed_at'], now());
        $newOrder['packer_user_id'] = $user->getKey();
        $newOrder['packed_at'] = $newOrder['order_closed_at'];
    }

    return $newOrder;
});
