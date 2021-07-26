<?php

namespace App\Modules\ShipmentConfirmationEmail\src;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class ServiceProvider
 * @package App\Modules\ShipmentConfirmationEmail\src
 */
class ServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Shipment Confirmation Email';

    /**
     * @var string
     */
    public static string $module_description = 'Sends email with shipping numbers';

    /**
     * @var bool
     */
    public bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderShipmentCreatedEvent::class => [
            Listeners\OrderShipmentCreatedEvent\SendShipmentConfirmationMailListener::class
        ]
    ];
}
