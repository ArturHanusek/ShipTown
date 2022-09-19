<?php

namespace App\Modules\Reports\src\Models;

use App\Models\DataCollectionRecord;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;

class DataCollectionReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Data Collection Report';

        $this->baseQuery = DataCollectionRecord::query()
            ->leftJoin('products as product', 'data_collection_records.product_id', '=', 'product.id')
            ->leftJoin('inventory', function ($query) {
                return $query->on('data_collection_records.product_id', '=', 'inventory.product_id')
                    ->where('inventory.warehouse_id', Auth::user()->warehouse_id);
            });

        $this->allowedIncludes = [
            'product',
            'dataCollection',
            'inventory',
        ];

        $this->fields = [
            'id'                    => 'data_collection_records.id',
            'product_id'            => 'data_collection_records.product_id',
            'data_collection_id'    => 'data_collection_records.data_collection_id',
            'product_sku'           => 'product.sku',
            'product_name'          => 'product.name',
            'quantity_requested'    => 'data_collection_records.quantity_requested',
            'quantity_scanned'      => 'data_collection_records.quantity_scanned',
            'quantity_to_scan'      => 'data_collection_records.quantity_to_scan',
            'inventory_quantity'    => 'inventory.quantity',
            'shelf_location'        => 'inventory.shelve_location',
        ];

        $this->casts = [
            'id'                    => 'integer',
            'data_collection_id'    => 'integer',
            'product_id'            => 'integer',
            'product_sku'           => 'string',
            'product_name'          => 'string',
            'shelf_location'        => 'string',
            'quantity_requested'    => 'float',
            'quantity_scanned'      => 'float',
            'quantity_to_scan'      => 'float',
            'inventory_quantity'    => 'float',
        ];

        $this->addFilter(
            AllowedFilter::callback('has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->withAllTags($value);
                });
            })
        );
    }
}
