<?php


namespace Shomisha\UnusualRelationships\Relationships;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

class HasManyThroughMany extends HasOneOrMany
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

    public function addConstraints()
    {
        $this->prepareSelectsAndJoins();

        $throughTable = $this->through->getTable();

        $this->query->where("{$throughTable}.{$this->foreignOnThrough}", $this->getParentKey());
    }

    public function addEagerConstraints(array $models)
    {
        $this->prepareSelectsAndJoins();

        $throughTable = $this->through->getTable();
        $keys = collect($models)->map(function (Model $model) {
            return $model->getKey();
        });

        $this->query->whereIn("{$throughTable}.{$this->foreignOnThrough}", $keys);
    }

    protected function prepareSelectsAndJoins()
    {
        $relatedTable = $this->related->getTable();
        $relatedKey = $this->related->getKeyName();

        $throughTable = $this->through->getTable();
        $throughKey = $this->through->getKeyName();

        $this->query->select([
            "{$relatedTable}.*",
            "{$throughTable}.{$this->foreignOnThrough}",
        ]);

        $this->query->join($this->pivotTable, "{$this->pivotTable}.{$this->relatedForeign}", '=', "{$relatedTable}.{$relatedKey}");
        $this->query->join($throughTable, "{$throughTable}.{$throughKey}", "=", "{$this->pivotTable}.{$this->throughForeign}");

        $this->query->distinct();
    }

    /**
     * @param Model[] $models
     * @param string $relation
     * @return array|void
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }
    }

    public function match(array $models, Collection $results, $relation)
    {
        return $this->matchMany($models, $results, $relation);
    }

    public function getResults()
    {
        return (is_null($this->getParentKey()))
            ? $this->related->newCollection()
            : $this->query->get();
    }
}