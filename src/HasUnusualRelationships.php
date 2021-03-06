<?php

namespace Shomisha\UnusualRelationships;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Shomisha\UnusualRelationships\Relationships\BelongsToManyThrough;

trait HasUnusualRelationships
{
    protected function belongsToManyThrough(string $related, string $through, string $foreign = null, string $pivot = null, string $pivotRelatedForeign = null, $pivotThroughForeign = null)
    {
        /** @var \Illuminate\Database\Eloquent\Model $relatedInstance */
        $relatedInstance = new $related;

        /** @var \Illuminate\Database\Eloquent\Model $throughInstance */
        $throughInstance = new $through;

        return new BelongsToManyThrough(
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

    /**
     * Guess the pivot table name for the provided Models.
     *
     * @param Model[] ...$models
     * @return string
     */
    protected function guessPivot(...$models)
    {
        return collect($models)->map(function (Model $model) {
            return Str::singular($model->getTable());
        })->sort()->join('_');
    }
}
