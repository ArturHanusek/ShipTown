<?php

namespace App\Modules\Rmsapi\src;

use App\Events\SyncRequestedEvent;
use App\Modules\BaseModuleServiceProvider;

class EventServiceProviderBase extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public string $module_name = 'RMSAPI Integration';

    /**
     * @var string
     */
    public string $module_description = 'Provides connectivity to Microsoft RMS 2.0';

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEvent\DispatchSyncJobsListener::class,
        ],
    ];
}
