<?php

namespace App;

use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 *
 */
class RoutesBuilder
{
    /**
     * @param string $name
     * @param array $options
     * @return PendingResourceRegistration
     */
    public static function apiResource(string $name, array $options = []): PendingResourceRegistration
    {
        $controllerClass  = static::getControllerClass($name);
        $routeName = static::getRouteName($name);
        $options['as'] = $routeName;

        return Route::apiResource($name, $controllerClass, $options);
    }

    /**
     * @param string $name
     * @return string
     */
    private static function getControllerClass(string $name): string
    {
        // $name = modules/dpd-uk/dpduk-connections (sample)

        $nameParts = collect(explode('/', $name));
        // nameParts['modules','dpd-uk','dpduk-connections']
        $nameParts->prepend('api');
        // nameParts['api','modules','dpd-uk','dpduk-connections']

        $controllerName = $nameParts->pop();
        // nameParts['api','modules','dpd-uk']
        // $controllerName = dpduk-connections
        $controllerName = Str::singular($controllerName);
        // $controllerName = dpduk-connection
        $controllerName = Str::camel($controllerName);
        // $controllerName = dpdukConnections
        $controllerName = Str::ucfirst($controllerName);
        // $controllerName = DpdukConnections

        $controllerName = $controllerName.'Controller';
        // $controllerName = DpdukConnectionsController


        // nameParts['api','modules','dpd-uk']
        $controllerPath = implode('\\', $nameParts->toArray());
        // $controllerPath = api\modules\dpd-uk
        $controllerPath = Str::title($controllerPath);
        // $controllerPath = Api\Modules\Dpd-Uk
        $controllerPath = Str::camel($controllerPath);
        // $controllerPath = Api\Modules\DpdUk

        // $controllerClass = Api\Modules\CustomModuleController
        return $controllerPath. '\\' . $controllerName;
    }

    /**
     * @param string $name
     * @return string
     */
    private static function getRouteName(string $name): string
    {
        // $name modules/dpd-uk-connections (sample)
        $parts = collect(explode('/', $name));
        // parts['modules','dpd-uk-connections']

        $parts->pop();
        // parts['modules']

        $parts->prepend('api');
        // parts['api','modules']

        // api.modules
        return $parts->implode('.');
    }
}
