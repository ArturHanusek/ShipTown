<?php

namespace App\Modules\Api2cart\src\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Api2cart\src\Models\Api2cartConnection.
 *
 * @property int         $id
 * @property string      $location_id
 * @property string      $type
 * @property string      $url
 * @property string      $prefix
 * @property string|null $bridge_api_key
 * @property int|null    $magento_store_id
 * @property string|null $magento_warehouse_id
 * @property string      $last_synced_modified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Api2cartConnection newModelQuery()
 * @method static Builder|Api2cartConnection newQuery()
 * @method static Builder|Api2cartConnection query()
 * @method static Builder|Api2cartConnection whereBridgeApiKey($value)
 * @method static Builder|Api2cartConnection whereMagentoStoreId($value)
 * @method static Builder|Api2cartConnection whereMagentoWarehouseId($value)
 * @method static Builder|Api2cartConnection whereCreatedAt($value)
 * @method static Builder|Api2cartConnection whereId($value)
 * @method static Builder|Api2cartConnection whereLastSyncedModifiedAt($value)
 * @method static Builder|Api2cartConnection whereLocationId($value)
 * @method static Builder|Api2cartConnection wherePrefix($value)
 * @method static Builder|Api2cartConnection whereType($value)
 * @method static Builder|Api2cartConnection whereUpdatedAt($value)
 * @method static Builder|Api2cartConnection whereUrl($value)
 * @mixin Eloquent
 * @property int|null $inventory_location_id
 * @property int|null $pricing_location_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Api2cart\src\Models\Api2cartProductLink[] $productLinks
 * @property-read int|null $product_links_count
 * @method static Builder|Api2cartConnection whereInventoryLocationId($value)
 * @method static Builder|Api2cartConnection wherePricingLocationId($value)
 */
class Api2cartConnection extends Model
{
    //
    protected $fillable = [
        'prefix',
        'bridge_api_key',
        'last_synced_modified_at',
        'url',
        'location_id',
        'type',
    ];

    protected $table = 'api2cart_connections';

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes([
            'last_synced_modified_at' => Carbon::now(),
        ], true);

        parent::__construct($attributes);
    }

    /**
     * @return HasMany
     */
    public function productLinks(): HasMany
    {
        return $this->hasMany(Api2cartProductLink::class);
    }
}
