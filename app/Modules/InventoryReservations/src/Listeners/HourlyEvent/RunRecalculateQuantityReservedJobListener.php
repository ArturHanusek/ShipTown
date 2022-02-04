<?php

namespace App\Modules\InventoryReservations\src\Listeners\HourlyEvent;

use App\Events\HourlyEvent;
use App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityReservedJob;

class RunRecalculateQuantityReservedJobListener
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     *
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        RecalculateQuantityReservedJob::dispatch();
    }
}
