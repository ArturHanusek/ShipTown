<?php

namespace App;

use App\Models\Warehouse;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel.
 *
 * @method static self create(array $array)
 *
 * @method static first()
 * @method static firstOrCreate(string[] $array, string[] $array1)
 * @method static firstOrUpdate(array $array, array $array1)
 * @method static findOrFail($id)
 * @method static updateOrCreate(array $array, array $array1)
 * @method static where($values)
 * @method static whereBetween(string $string, array $array)
 * @method static whereId(int $id)
 *
 * @mixin Eloquent
 */
abstract class BaseModel extends Model
{
    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function isAttributeChanged(string $attribute): bool
    {
        return $this->getOriginal($attribute) !== $this->getAttribute($attribute);
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function isAttributeNotChanged(string $attribute): bool
    {
        return !$this->isAttributeChanged($attribute);
    }

    /**
     * @param array $attributes
     *
     * @return bool
     */
    public function isAnyAttributeChanged(array $attributes): bool
    {
        $changedAttributes = collect($attributes)->filter(function (string $attribute) {
            return $this->isAttributeChanged($attribute);
        });

        return $changedAttributes->isNotEmpty();
    }
}
