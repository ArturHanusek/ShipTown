<?php

namespace App\Modules\SplitOrder\src;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Warehouse;
use Exception;

/**
 *
 */
class SplitOrderService
{
    /**
     * @var Warehouse
     */
    private Warehouse $warehouse;

    /**
     * @var Order|null
     */
    private ?Order $newOrder = null;

    /**
     * @var string|null
     */
    private ?string $newOrderStatus;

    /**
     * @var Order
     */
    private Order $originalOrder;

    public function split(Order $order, Warehouse $warehouse, string $newOrderStatus)
    {
        $this->newOrderStatus = $newOrderStatus;
        $this->warehouse = $warehouse;

        if ($order->lockForEditing()) {
            $this->originalOrder = $order->refresh();
            $this->extractFulfillableProducts();
            $order->unlockFromEditing();
        }
    }

    /**
     * @return void
     */
    private function extractFulfillableProducts(): void
    {
        $this->originalOrder->orderProducts
            ->filter(function (OrderProduct $orderProductOriginal) {
                return $orderProductOriginal->quantity_to_ship > 0;
            })
            ->each(function (OrderProduct $orderProduct) {
                /** @var Inventory $inventory */
                $inventory = $orderProduct->product->inventory()
                    ->where(['warehouse_id' => $this->warehouse->getKey()])
                    ->first();

                $quantityToExtract = min($inventory->quantity_available, $orderProduct->quantity_to_ship);

                if ($quantityToExtract <= 0.00) {
                    return true;
                }

                $this->extractOrderProduct($orderProduct, $quantityToExtract, $inventory);
                return true;
            });

        if ($this->newOrder) {
            $this->newOrder->unlockFromEditing();
        }
    }

    /**
     * @return Order
     */
    private function getNewOrderOrCreate(): Order
    {
        if ($this->newOrder === null) {
            $newOrderNumber = $this->originalOrder->order_number . '-PARTIAL-' . $this->warehouse->code;

            $this->newOrder = $this->originalOrder->replicate();
            $this->newOrder->status_code = $this->newOrderStatus;
            $this->newOrder->is_editing = true;
            $this->newOrder->order_number = $newOrderNumber;

            try {
                $this->newOrder->save();
            } catch (Exception $exception) {
                $this->newOrder = Order::whereOrderNumber($newOrderNumber)->first();
            }
        }

        return $this->newOrder;
    }

    /**
     * @param OrderProduct $orderProduct
     * @param int $quantity
     * @param Inventory $inventory
     */
    private function extractOrderProduct(OrderProduct $orderProduct, int$quantity, Inventory $inventory): void
    {
        $newOrderProduct = $orderProduct->replicate([
            'order_id',
            'quantity_ordered',
            'quantity_split',
            'quantity_to_ship',
            'quantity_picked',
            'quantity_skipped_picking',
        ]);

        $newOrderProduct->order()->associate($this->getNewOrderOrCreate());
        $newOrderProduct->save();

        $recordsUpdatedCount = OrderProduct::query()
            ->whereId($orderProduct->getKey())
            ->whereUpdatedAt($orderProduct->updated_at)
            ->update([
                'quantity_split' => $orderProduct->quantity_split + $quantity,
                'updated_at' => now(),
            ]);

        if ($recordsUpdatedCount === 1) {
            $newOrderProduct->increment('quantity_ordered', $quantity);
            $inventory->increment('quantity_reserved', $quantity);
        }
    }
}
