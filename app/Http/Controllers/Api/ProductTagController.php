<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Taggable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Tags\Tag;

/**
 * Class ProductTagController.
 */
class ProductTagController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $query = QueryBuilder::for(Taggable::class)
            ->allowedSorts(['created_at', 'updated_at', 'tag.name'])
            ->allowedFilters(['taggable_type', 'taggable_id'])
            ->allowedIncludes(['tag']);

        return JsonResource::collection($this->getPaginatedResult($query));
    }
}
