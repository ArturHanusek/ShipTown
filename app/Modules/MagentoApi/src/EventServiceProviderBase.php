<?php

namespace App\Modules\MagentoApi\src;

use App\Events\HourlyEvent;
use App\Events\Product\ProductTagAttachedEvent;
use App\Events\Product\ProductTagDetachedEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Magento 2.0 API';

    /**
     * @var string
     */
    public static string $module_description = 'Module provides connectivity to Magento 2.0 API';

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
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEvent\DispatchSyncCheckFailedProductsJobListener::class,
        ],

        HourlyEvent::class => [
            Listeners\HourlyEvent\SyncProductsListener::class,
        ],

        ProductTagAttachedEvent::class => [
            Listeners\ProductTagAttachedEvent\SyncWhenOutOfStockAttachedListener::class,
        ],

        ProductTagDetachedEvent::class => [
            Listeners\ProductTagDetachedEvent\SyncWhenOutOfStockDetachedListener::class,
        ],
    ];
}
