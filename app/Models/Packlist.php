<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

/**
 * Class Packlist
 * @property \Illuminate\Support\Carbon|null packed_at
 * @property int order_id
 * @package App\Models
 */
class Packlist extends Model
{

    protected $fillable = [
        'order_id',
        'order_product_id',
        'product_id',
        'location_id',
        'sku_ordered',
        'name_ordered',
        'quantity_requested',
        'quantity_packed',
        'packer_user_id',
        'packed_at',
        'is_packed',
    ];

    protected $appends = [
        'is_packed'
    ];

    /**
     * @param Builder $query
     * @param int $inventory_location_id
     * @return Builder
     */
    public function scopeAddInventorySource($query, $inventory_location_id)
    {
        $source_inventory = Inventory::query()
            ->select([
                'shelve_location as inventory_source_shelf_location',
                'quantity as inventory_source_quantity',
                'product_id as inventory_source_product_id',
            ])
            ->where(['location_id'=>$inventory_location_id])
            ->toBase();

        return $query->leftJoinSub($source_inventory, 'inventory_source', function ($join) {
            $join->on('packlists.product_id', '=', 'inventory_source_product_id');
        });
    }

    public function getIsPackedAttribute()
    {
        return $this->packed_at !== null;
    }

    public function setIsPackedAttribute($value)
    {
        $this->packed_at = $value ? now() : null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'picker_user_id');
    }
}
