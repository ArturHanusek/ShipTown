<?php

namespace App\Modules\QueueMonitor\src;

use App\Modules\BaseModuleServiceProvider;
use App\Modules\QueueMonitor\src\Dispatcher\QueueMonitorDispatcher;
use Exception;
use Illuminate\Bus\Dispatcher;

/**
 * Class Api2cartServiceProvider.
 */
class QueueMonitorServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Queue Monitor';

    /**
     * @var string
     */
    public static string $module_description = 'Logs jobs dispatched to the queue.';

    /**
     * @var string
     */
    public static string $settings_link = '';

    /**
     * @var bool
     */
    public static bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * @throws Exception
     */
    public function boot()
    {
        parent::boot();

        $this->app->extend(Dispatcher::class, function ($dispatcher, $app) {
            return new QueueMonitorDispatcher($app, $dispatcher);
        });
    }
}
