<?php

namespace App\Modules\StockControl\src;

use App\Events\OrderProductShipmentCreatedEvent;
use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class ServiceProvider
 * @package App\Modules\ShipmentConfirmationEmail\src
 */
class StockControlServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Stock Control';

    /**
     * @var string
     */
    public static string $module_description = 'Increase \ Decrease stock when product shipped';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderProductShipmentCreatedEvent::class => [
            Listeners\OrderProductShipmentCreatedEvent\IncreaseDecreaseInventoryQuantityListener::class
        ]
    ];
}
