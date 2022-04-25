<?php

namespace App\Observers;

use App\Events\Order\OrderUpdatedEvent;
use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Models\OrderProduct;

class OrderProductObserver
{
    /**
     * Handle the order product "created" event.
     *
     * @param OrderProduct $orderProduct
     *
     * @return void
     */
    public function created(OrderProduct $orderProduct)
    {
        optional($orderProduct->product, function () use ($orderProduct) {
            $orderProduct->product->log('order placed', [
                'order_number' => $orderProduct->order->order_number,
                'quantity_ordered' => $orderProduct->quantity_ordered,
            ]);
        });

        OrderProductCreatedEvent::dispatch($orderProduct);
    }

    /**
     * Handle the order product "updated" event.
     *
     * @param OrderProduct $orderProduct
     *
     * @return void
     */
    public function updated(OrderProduct $orderProduct)
    {
        $this->setOrdersPickedAtIfAllPicked($orderProduct);

        OrderProductUpdatedEvent::dispatch($orderProduct);

        // we do it here because touch() does not dispatch models UpdatedEvent
        OrderUpdatedEvent::dispatch($orderProduct->order);
    }

    /**
     * @param OrderProduct $orderProduct
     */
    private function setOrdersPickedAtIfAllPicked(OrderProduct $orderProduct): void
    {
        $orderHasMoreToPick = OrderProduct::query()
            ->where('quantity_to_pick', '>', 0)
            ->where(['order_id' => $orderProduct->order_id])
            ->exists();

        if ($orderHasMoreToPick === false) {
            $orderProduct->order()->update(['picked_at' => now()]);
        }
    }
}
