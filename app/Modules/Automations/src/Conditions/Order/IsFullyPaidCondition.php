<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use Log;

/**
 *
 */
class IsFullyPaidCondition
{
    /**
     * @var ActiveOrderCheckEvent|OrderCreatedEvent|OrderUpdatedEvent
     */
    private $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * @param bool $condition_value
     * @return bool
     */
    public function isValid(bool $condition_value): bool
    {
        $result = $this->event->order->isPaid === $condition_value;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected_isPaid' => $condition_value,
            'actual_isPaid' => $this->event->order->isPaid,
        ]);

        return $result;
    }
}
