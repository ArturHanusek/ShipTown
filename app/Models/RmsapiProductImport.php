<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\RmsapiProductImport.
 *
 * @property int         $id
 * @property int         $connection_id
 * @property string|null $batch_uuid
 * @property string|null $when_processed
 * @property int|null    $product_id
 * @property string|null $sku
 * @property array       $raw_import
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|RmsapiProductImport newModelQuery()
 * @method static Builder|RmsapiProductImport newQuery()
 * @method static Builder|RmsapiProductImport query()
 * @method static Builder|RmsapiProductImport whereBatchUuid($value)
 * @method static Builder|RmsapiProductImport whereConnectionId($value)
 * @method static Builder|RmsapiProductImport whereCreatedAt($value)
 * @method static Builder|RmsapiProductImport whereId($value)
 * @method static Builder|RmsapiProductImport whereProductId($value)
 * @method static Builder|RmsapiProductImport whereRawImport($value)
 * @method static Builder|RmsapiProductImport whereSku($value)
 * @method static Builder|RmsapiProductImport whereUpdatedAt($value)
 * @method static Builder|RmsapiProductImport whereWhenProcessed($value)
 * @mixin Eloquent
 */
class RmsapiProductImport extends Model
{
    protected $table = 'modules_rmsapi_products_imports';

    protected $fillable = [
        'connection_id',
        'batch_uuid',
        'when_processed',
        'product_id',
        'sku',
        'raw_import',
    ];

    protected $casts = [
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'raw_import' => '{}',
    ];
}
