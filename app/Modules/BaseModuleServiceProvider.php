<?php

namespace App\Modules;

use App;
use App\Models\Module;
use Exception;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Log;

/**
 * Class BaseModuleServiceProvider.
 */
abstract class BaseModuleServiceProvider extends EventServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '';

    /**
     * @var string
     */
    public static string $module_description = '';

    /**
     * Should we automatically enable it
     * When module first registered.
     *
     * @var bool
     */
    public bool $autoEnable = false;

    /**
     * Register any events for your application.
     *
     * @throws Exception
     *
     * @return void
     */
    public function boot()
    {
        if (empty(static::$module_name) or empty(static::$module_description)) {
            throw new Exception('Module "'.get_called_class().'" missing name or description');
        }

        if ($this->isEnabled()) {
            parent::boot();
        }
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        try {
            $module = Module::firstOrCreate([
                'service_provider_class' => get_called_class(),
            ], [
                'enabled' => $this->autoEnable,
            ]);

            return $module->enabled;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     *
     */
    public static function enableModule()
    {
        $module = Module::firstOrCreate(['service_provider_class' => get_called_class()], ['enabled' => false]);

        if ($module->enabled) {
            return;
        }

        $module->enabled = true;
        $module->save();

        App::register(get_called_class())
            ->boot();
    }

    /**
     * @return bool
     */
    public static function uninstallModule(): bool
    {
        try {
            $module = Module::where(['service_provider_class' => get_called_class()])->first();

            if ($module) {
                $module->delete();
            }

            return true;
        } catch (Exception $exception) {
            Log::emergency($exception);
            return false;
        }
    }

    /**
     *
     */
    public static function disableModule()
    {
        $module = Module::firstOrCreate(['service_provider_class' => get_called_class()], ['enabled' => false]);

        if ($module->enabled) {
            $module->enabled = false;
            $module->save();
        }
    }
}