<?php

namespace App\Modules\AutoTags\src\Listeners\InventoryUpdatedEvent;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Modules\AutoTags\src\Jobs\ToggleOversoldTagJob;

class ToggleProductOversoldTagListener
{
    /**
     * Handle the event.
     *
     * @param InventoryUpdatedEvent $event
     *
     * @return void
     */
    public function handle(InventoryUpdatedEvent $event)
    {
        ToggleOversoldTagJob::dispatch($event->getInventory()->product_id)
            ->delay(60);
    }
}
