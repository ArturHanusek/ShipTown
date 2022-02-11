<?php

namespace App\Modules\Api2cart\src\Models;

use App\BaseModel;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * App\Modules\Api2cart\src\Models\Api2cartOrderImports.
 *
 * @property int            $id
 * @property int|null       $connection_id
 * @property int|null       $order_id
 * @property string|null    $when_processed
 * @property string|null    $order_number
 * @property integer|null   $api2cart_order_id
 * @property string|null    $shipping_method_name
 * @property string|null    $shipping_method_code
 * @property array          $raw_import
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @property-read Api2cartConnection $api2cartConnection
 *
 * @method static Builder|Api2cartOrderImports newModelQuery()
 * @method static Builder|Api2cartOrderImports newQuery()
 * @method static Builder|Api2cartOrderImports query()
 * @method static Builder|Api2cartOrderImports whereConnectionId($value)
 * @method static Builder|Api2cartOrderImports whereCreatedAt($value)
 * @method static Builder|Api2cartOrderImports whereId($value)
 * @method static Builder|Api2cartOrderImports whereOrderId($value)
 * @method static Builder|Api2cartOrderImports whereOrderNumber($value)
 * @method static Builder|Api2cartOrderImports whereRawImport($value)
 * @method static Builder|Api2cartOrderImports whereUpdatedAt($value)
 * @method static Builder|Api2cartOrderImports whereWhenProcessed($value)
 * @mixin Eloquent
 */
class Api2cartOrderImports extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'modules_api2cart_order_imports';

    /**
     * @var string[]
     */
    protected $fillable = [
        'connection_id',
        'when_processed',
        'order_number',
        'raw_import',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    /**
     * @var string[]
     */
    protected $attributes = [
        'raw_import' => '{}',
    ];

    public function save(array $options = [])
    {
        $this->order_number = $this->raw_import['id'];
        $this->api2cart_order_id = $this->raw_import['order_id'];
        $this->shipping_method_name = $this->raw_import['shipping_method']['name'];
        if (Arr::has($this->raw_import['shipping_method'], 'additional_fields')) {
            $this->shipping_method_code = $this->raw_import['shipping_method']['additional_fields']['code'];
        }

        return parent::save($options);
    }

    /**
     * @return BelongsTo
     */
    public function api2cartConnection(): BelongsTo
    {
        return $this->belongsTo(Api2cartConnection::class, 'connection_id');
    }

    /**
     * @return array
     */
    public function extractShippingAddressAttributes(): array
    {
        $shipping_address = $this->raw_import['shipping_address'];

        // array_filter will cleanup null values
        return array_filter([
            'company' => $shipping_address['company'],
            'gender' => $shipping_address['gender'],
            'first_name' => $shipping_address['first_name'],
            'last_name' => $shipping_address['last_name'],
            'email' => $this->raw_import['customer']['email'],
            'address1' => $shipping_address['address1'],
            'address2' => $shipping_address['address2'],
            'postcode' => $shipping_address['postcode'],
            'city' => $shipping_address['city'],
            'state_code' => $shipping_address['state'] ? $shipping_address['state']['code'] : '',
            'state_name' => $shipping_address['state'] ? $shipping_address['state']['name'] : '',
            'country_code' => $shipping_address['country']['code3'],
            'country_name' => $shipping_address['country']['name'],
            'phone' => $shipping_address['phone'],
            'fax' => $shipping_address['fax'],
            'website' => $shipping_address['website'],
            'region' => $shipping_address['region'],
        ]);
    }

    /**
     * @return array
     */
    public function extractOrderProducts(): array
    {
        $result = [];

        foreach ($this->raw_import['order_products'] as $rawOrderProduct) {
            $result[] = [
                'sku_ordered' => $rawOrderProduct['model'],
                'name_ordered' => $rawOrderProduct['name'],
                'quantity_ordered' => $rawOrderProduct['quantity'],

                // to take any discounts into consideration,
                // we recalculate unit price based on lines total_price
                // total_price has discounts already taken off
                'price' => $rawOrderProduct['total_price'] / $rawOrderProduct['quantity'],
            ];
        }

        return $result;
    }

    /**
     * @param array|null $order
     * @param bool $chronological
     *
     * @return Collection
     */
    public function extractStatusHistory(array $order = null, bool $chronological = true): Collection
    {
        $statuses = Collection::make($this['raw_import']['status']['history']);

        if ($chronological) {
            $statuses = $statuses->sort(function ($a, $b) {
                $a_time = Carbon::make($a['modified_time']['value']);
                $b_time = Carbon::make($b['modified_time']['value']);

                return $a_time > $b_time;
            });
        }

        return $statuses;
    }

    /**
     * @return Carbon
     */
    public function ordersCreateAt(): Carbon
    {
        return Carbon::createFromTimeString($this->raw_import['create_at']['value']);
    }
}
