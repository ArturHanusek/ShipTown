<?php

namespace App\Modules\SystemHeartbeats\src;

use App\Events\DailyEvent;
use App\Events\EveryFiveMinutesEvent;
use App\Events\EveryMinuteEvent;
use App\Events\EveryTenMinutesEvent;
use App\Events\HourlyEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class SystemHeartbeatsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'System Heartbeats';

    /**
     * @var string
     */
    public static string $module_description = 'This module monitors core system functionalities like events and jobs';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],

        EveryFiveMinutesEvent::class => [
            Listeners\EveryFiveMinutesEventListener::class,
        ],

        EveryTenMinutesEvent::class => [
            Listeners\EveryTenMinutesEventListener::class,
        ],

        HourlyEvent::class => [
            Listeners\HourlyEventListener::class,
        ],

        DailyEvent::class => [
            Listeners\DailyEventListener::class,
        ]
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
