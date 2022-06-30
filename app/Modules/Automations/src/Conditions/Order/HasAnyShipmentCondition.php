<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class HasAnyShipmentCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        if ($expected_value === '') {
            $expected_value = 'true';
        }

        if (filter_var($expected_value, FILTER_VALIDATE_BOOL) === true) {
            return $query->whereHas('orderShipments');
        };

        return $query->whereDoesntHave('orderShipments');
    }
}
