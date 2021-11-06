<?php

namespace App\Modules\DpdUk\src\Models;

use App\Models\OrderAddress;
use App\Traits\Encryptable;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Configuration.
 *
 * @property int            $id
 * @property string         $username
 * @property string         $password
 * @property string         $account_number
 * @property int            $collection_address_id
 * @property string         $geo_session
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @property OrderAddress collectionAddress
 *
 * @method static Builder|Connection newModelQuery()
 * @method static Builder|Connection newQuery()
 * @method static Builder|Connection query()
 * @method static Builder|Connection first()
 * @method static Builder|Connection firstOrFail()
 * @mixin Eloquent
 *
 */
class Connection extends Model
{
    use Encryptable;

    protected $table = 'modules_dpduk_connections';

    protected $fillable = [
        'username',
        'password',
        'account_number',
        'collection_address_id',
        'geo_session',
    ];

    protected array $encryptable = [
        'username',
        'password',
        'account_number',
        'geo_session'
    ];

    /**
     * @return BelongsTo
     */
    public function collectionAddress(): BelongsTo
    {
        return $this->belongsTo(OrderAddress::class);
    }
}
