<?php


namespace App\Modules\Api2cart\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\Api2cart\src\Jobs\SyncOrderStatus;

class SyncStatusListener
{
    /**
     */
    public function handle(OrderUpdatedEvent $event)
    {
        if ($event->order->isAttributeNotChanged('status_code')) {
            return;
        }

        if (! $event->order->orderStatus->sync_ecommerce) {
            return;
        }

        SyncOrderStatus::dispatch($event->order);
    }
}