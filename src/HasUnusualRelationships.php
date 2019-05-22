<?php


namespace Shomisha\UnusualRelationships;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Shomisha\UnusualRelationships\Relationships\HasManyThroughMany;

trait HasUnusualRelationships
{
    protected function hasManyThroughMany(string $related, string $through, string $foreign = null, string $pivot = null, string $pivotRelatedForeign = null, $pivotThroughForeign = null)
    {
        /** @var \Illuminate\Database\Eloquent\Model $relatedInstance */
        $relatedInstance = new $related;

        /** @var \Illuminate\Database\Eloquent\Model $throughInstance */
        $throughInstance = new $through;

        return new HasManyThroughMany(
            $relatedInstance->newQuery(),
            $this,
            $relatedInstance,
            $throughInstance,
            $foreign ?? $this->getForeignKey(),
            $pivot ?? $this->guessPivot($relatedInstance, $throughInstance),
            $pivotRelatedForeign ?? $relatedInstance->getForeignKey(),
            $pivotThroughForeign ?? $throughInstance->getForeignKey()
        );
    }

    protected function hasManyThroughOne(string $related, string $one)
    {

    }

    /**
     * Guess the pivot table name for the provided Models.
     *
     * @param Model[] ...$models
     * @return string
     */
    protected function guessPivot(...$models)
    {
        $tables = collect($models)->map(function (Model $model) {
            return Str::singular($model->getTable());
        })->sort();

        return $tables->join('_');
    }
}