<?php

namespace Shomisha\UnusualRelationships\Relationships;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BelongsToManyThrough extends HasMany
{
    /** @var Model */
    protected $through;

    /** @var string */
    protected $foreignOnThrough;

    /** @var string */
    protected $pivotTable;

    /** @var string */
    protected $relatedForeign;

    /** @var string */
    protected $throughForeign;

    public function __construct(
        Builder $query,
        Model $parent,
        Model $related,
        Model $through,
        string $foreignOnThrough,
        string $pivotTable,
        string $relatedForeign,
        string $throughForeign
    ) {
        $this->through = $through;
        $this->related = $related;
        $this->foreignOnThrough = $foreignOnThrough;
        $this->pivotTable = $pivotTable;
        $this->relatedForeign = $relatedForeign;
        $this->throughForeign = $throughForeign;

        parent::__construct($query, $parent, $foreignOnThrough, $parent->getKeyName());
    }

    /**
     * Constrain the query.
     */
    public function addConstraints()
    {
        $relatedTable = $this->related->getTable();
        $relatedKey = $this->related->getKeyName();

        $throughTable = $this->through->getTable();
        $throughKey = $this->through->getKeyName();

        $this->query->select([
            "{$relatedTable}.*",
            "{$throughTable}.{$this->foreignOnThrough}",
        ]);

        $this->query->join("{$this->pivotTable} as pivot_table", "pivot_table.{$this->relatedForeign}", '=', "{$relatedTable}.{$relatedKey}");
        $this->query->join("{$throughTable}", "{$throughTable}.{$throughKey}", '=', "pivot_table.{$this->throughForeign}");

        $this->query->distinct();

        if (static::$constraints) {
            $this->query->where("{$throughTable}.{$this->foreignOnThrough}", $this->getParentKey());
        }
    }

    /**
     * Constrain the query for multiple models.
     *
     * @param array $models
     */
    public function addEagerConstraints(array $models)
    {
        $keys = collect($models)->map(function (Model $model) {
            return $model->getKey();
        });

        $throughTable = $this->through->getTable();

        $this->query->whereIn("{$throughTable}.{$this->foreignOnThrough}", $keys);
    }
}
