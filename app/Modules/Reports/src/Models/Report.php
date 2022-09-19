<?php

namespace App\Modules\Reports\src\Models;

use App\Exceptions\InvalidSelectException;
use App\Helpers\CsvBuilder;
use App\Modules\Reports\src\Http\Resources\ReportResource;
use App\Traits\HasTagsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Spatie\QueryBuilder\QueryBuilder;

class Report extends Model
{
    use HasTagsTrait;

    protected $table = 'report';
    public string $report_name = 'Report';
    public string $view = 'reports.inventory';

    protected string $defaultSelect = '';
    protected ?string $defaultSort = null;

    public array $toSelect = [];

    public array $fields = [];

    public array $initial_data = [];
    /**
     * @var mixed
     */
    public $baseQuery;

    private array $allowedFilters = [];
    public array $allowedIncludes = [];
    private array $fieldAliases = [];

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidSelectException
     */
    public function response($request)
    {
        if ($request->has('filename')) {
            return $this->csvDownload();
        }

        return $this->view();
    }

    /**
     *
     */
    public function toArray()
    {
        return $this->queryBuilder()
            ->simplePaginate(request()->get('per_page', 10))
            ->appends(request()->query());
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws InvalidSelectException
     */
    public function queryBuilder(): QueryBuilder
    {
        $this->fieldAliases = [];

        foreach ($this->fields as $alias => $field) {
            $this->fieldAliases[] = $alias;
        }

        $queryBuilder = QueryBuilder::for($this->baseQuery);

        $queryBuilder = $this->addSelectFields($queryBuilder);

        if ($this->defaultSort) {
            $queryBuilder = $queryBuilder->defaultSort($this->defaultSort);
        }

        return $queryBuilder
            ->allowedFilters($this->getAllowedFilters())
            ->allowedSorts($this->fieldAliases)
            ->allowedIncludes($this->allowedIncludes);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function view()
    {
        try {
            $queryBuilder = $this->queryBuilder()
                ->limit(request('per_page', $this->perPage));
        } catch (InvalidFilterQuery | InvalidSelectException $ex) {
            return response($ex->getMessage(), $ex->getStatusCode());
        }

        $resource = ReportResource::collection($queryBuilder->get());

        $data = [
            'report_name' => $this->report_name ?? $this->table,
            'fields' => $resource->count() > 0 ? array_keys((array)json_decode($resource[0]->toJson())) : [],
            'data' => $resource,
        ];

        return view($this->view, $data);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidSelectException
     */
    public function csvDownload()
    {
        $csv = CsvBuilder::fromQueryBuilder(
            $this->queryBuilder(),
            $this->fieldAliases
        );

        return response((string)$csv, 200, [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . request('filename', 'report.csv') . '"',
        ]);
    }

    /**
     * @param AllowedFilter $filter
     * @return $this
     */
    public function addFilter(AllowedFilter $filter): Report
    {
        $this->allowedFilters[] = $filter;

        return $this;
    }

    /**
     * @param $include
     * @return $this
     */
    public function addAllowedInclude($include): Report
    {
        $this->allowedIncludes[] = $include;

        return $this;
    }

    /**
     * @return array
     */
    private function getAllowedFilters(): array
    {
        $filters = collect($this->allowedFilters);

        $filters = $filters->merge($this->addExactFilters());
        $filters = $filters->merge($this->addContainsFilters());
        $filters = $filters->merge($this->addBetweenFilters());
        $filters = $filters->merge($this->addBetweenDatesFilters());
        $filters = $filters->merge($this->addGreaterThan());
        $filters = $filters->merge($this->addLowerThan());

        return $filters->toArray();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidSelectException
     */
    private function addSelectFields(QueryBuilder $queryBuilder): QueryBuilder
    {
        $requestedSelect = collect(explode(',', request()->get('select', $this->defaultSelect)))->filter();

        if ($requestedSelect->isEmpty()) {
            $requestedSelect = collect(array_keys($this->fields));
        }

        $requestedSelect
            ->each(function ($selectFieldName) use ($queryBuilder) {
                $fieldValue = data_get($this->fields, $selectFieldName);

                if ($fieldValue === null) {
                    throw new InvalidSelectException('Requested select field(s) `' . $selectFieldName . '` are not allowed.
                    Allowed select(s) are ' . collect(array_keys($this->fields))->implode(','));
                }

                if ($fieldValue instanceof Expression) {
                    $queryBuilder->addSelect(DB::raw('(' . $fieldValue . ') as ' . $selectFieldName));
                    return;
                }

                $queryBuilder->addSelect($fieldValue . ' as ' . $selectFieldName);
            });

        return $queryBuilder;
    }

    /**
     * @return array
     */
    private function addExactFilters(): array
    {

        $allowedFilters = [];

        // add exact filters
        collect($this->fields)
            ->each(function ($full_field_name, $alias) use (&$allowedFilters) {
                $allowedFilters[] = AllowedFilter::callback($alias, function ($query, $value) use ($full_field_name) {
                    return $query->where($full_field_name, '=', $value);
                });
            });

        return $allowedFilters;
    }


    /**
     * @return array
     */
    private function addContainsFilters(): array
    {
        $allowedFilters = [];

        collect($this->fields)
            ->filter(function ($value, $key) {
                $type = data_get($this->casts, $key);

                return in_array($type, ['string', null]);
            })
            ->each(function ($record, $alias) use (&$allowedFilters) {
                $filterName = $alias . '_contains';

                $allowedFilters[] = AllowedFilter::partial($filterName, $record);
            });

        return $allowedFilters;
    }

    /**
     * @return array
     */
    private function addBetweenFilters(): array
    {
        $allowedFilters = [];

        collect($this->casts)
            ->filter(function ($type) {
                return $type === 'float';
            })
            ->each(function ($fieldType, $fieldAlias) use (&$allowedFilters) {
                $filterName = $fieldAlias . '_between';
                $fieldQuery = $this->fields[$fieldAlias];

                $allowedFilters[] = AllowedFilter::callback($filterName, function ($query, $value) use ($fieldType, $fieldAlias, $fieldQuery) {
                    // we add this to make sure query returns no records if array of two values is not specified
                    if ((!is_array($value)) or (count($value) != 2)) {
                        $query->whereRaw('1=2');
                        return;
                    }

                    if ($fieldQuery instanceof Expression) {
                        $query->whereBetween(DB::raw('(' . $fieldQuery . ')'), [floatval($value[0]), floatval($value[1])]);

                        return;
                    }

                    $query->whereBetween($fieldQuery, [floatval($value[0]), floatval($value[1])]);
                });
            });

        return $allowedFilters;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function addBetweenDatesFilters(): array
    {
        $allowedFilters = [];

        collect($this->casts)
            ->filter(function ($type) {
                return $type === 'datetime';
            })
            ->each(function ($fieldType, $fieldAlias) use (&$allowedFilters) {
                $filterName = $fieldAlias . '_between';
                $fieldQuery = $this->fields[$fieldAlias];

                $allowedFilters[] = AllowedFilter::callback($filterName, function ($query, $value) use ($fieldType, $fieldAlias, $filterName, $fieldQuery) {
                    // we add this to make sure query returns no records if array of two values is not specified
                    if ((!is_array($value)) or (count($value) != 2)) {
                        throw new \Exception($filterName . ': Invalid filter value, expected array of two values');
                    }

                    if ($fieldQuery instanceof Expression) {
                        $query->whereBetween(
                            DB::raw('(' . $fieldQuery . ')'),
                            [Carbon::parse($value[0]), Carbon::parse($value[1])]
                        );

                        return;
                    }

                    $query->whereBetween($fieldQuery, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
                });
            });

        return $allowedFilters;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function addGreaterThan(): array
    {
        $allowedFilters = [];

        collect($this->casts)
            ->filter(function ($type) {
                return in_array($type, ['string', 'datetime']);
            })
            ->each(function ($record, $alias) use (&$allowedFilters) {
                $filterName = $alias . '_greater_than';

                $allowedFilters[] = AllowedFilter::callback($filterName, function ($query, $value) use ($alias, $filterName) {
                    $query->where($this->fields[$alias], '>', $value);
                });
            });

        return $allowedFilters;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function addGreaterThanFloat(): array
    {
        $allowedFilters = [];

        collect($this->casts)
            ->filter(function ($type) {
                return $type === 'float';
            })
            ->each(function ($record, $alias) use (&$allowedFilters) {
                $filterName = $alias . '_greater_than';

                $allowedFilters[] = AllowedFilter::callback($filterName, function ($query, $value) use ($alias, $filterName) {
                    $query->where($this->fields[$alias], '>', floatval($value));
                });
            });

        return $allowedFilters;
    }

    /**
     * @return array
     */
    private function addLowerThan(): array
    {
        $allowedFilters = [];

        collect($this->casts)
            ->filter(function ($type) {
                return $type === 'float';
            })
            ->each(function ($record, $alias) use (&$allowedFilters) {
                $filterName = $alias . '_lower_than';

                $allowedFilters[] = AllowedFilter::callback($filterName, function ($query, $value) use ($alias) {
                    // we add this to make sure query returns no records if array of two values is not specified
                    if ((!is_array($value)) or (count($value) != 2)) {
                        $query->whereRaw('1=2');
                        return;
                    }

                    $query->where($this->fields[$alias], '<', floatval($value));
                });
            });

        return $allowedFilters;
    }
}
