<?php

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Order::class, rand(180, 250))
            ->with('orderProducts', rand(1,4))
            ->create([
                'status_code' => 'processing'
            ]);

        factory(Order::class, rand(280, 350))
            ->with('orderProducts', rand(1,4))
            ->create([
                'status_code' => 'picking'
            ]);

        factory(Order::class, rand(20,50))
            ->with('orderProducts', rand(1,4))
            ->create([
                'status_code' => 'holded'
            ]);

        factory(Order::class, rand(30,50))
            ->with('orderProducts', rand(1,4))
            ->create([
                'status_code' => 'canceled'
            ]);

        factory(Order::class, rand(60,80))
            ->with('orderProducts', rand(1,4))
            ->create([
                'status_code' => 'complete'
            ]);

        factory(Order::class, rand(20,50))
            ->with('orderProducts', rand(1,4))
            ->create([
                'status_code' => 'completed_imported_to_rms'
            ]);

//        factory(Order::class, 2)
//            ->create()
//            ->each(function (Order $order) {
//                $orderProducts = factory(OrderProductController::class, 1)->make();
//
//                $order->orderProducts()->saveMany($orderProducts);
//            });
//
//        // we fabricate few orders with SKU not present in database
//        factory(Order::class, 1)
//            ->create()
//            ->each(function (Order $order) {
//                $orderProducts = collect(factory(OrderProductController::class, 1)->make())
//                    ->map(function ($orderProduct) {
//
//                        $suffix = Arr::random(['-blue', '-red', '-green', '-xl', '-small-orange']);
//
//                        $orderProduct['product_id'] = null;
//                        $orderProduct['sku_ordered'] = $orderProduct['sku_ordered'] . $suffix;
//                        $orderProduct['name_ordered'] = $orderProduct['name_ordered'] . $suffix;
//
//                        return $orderProduct;
//                    });
//
//                $order->orderProducts()->saveMany($orderProducts);
//            });
    }
}
