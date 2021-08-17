<?php

namespace App\Modules\Automations\src\Models;

use App\BaseModel;

/**
 * @property string validation_class
 * @property string condition_value
 */
class Condition extends BaseModel
{
    protected $table = 'modules_automations_conditions';

    protected $fillable = [
        'automation_id',
        'priority',
        'validation_class',
        'condition_value',
    ];
}
