<?php

namespace Shomisha\UnusualRelationships\Test\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Shomisha\UnusualRelationships\Test\Models\Flight;
use Shomisha\UnusualRelationships\Test\Models\Member;
use Shomisha\UnusualRelationships\Test\Models\Performer;
use Shomisha\UnusualRelationships\Test\Unit\Abstractions\HasManyThroughTest;

class BelongsToManyThroughTest extends HasManyThroughTest
{
    protected function createParents(int $count = 1): Collection
    {
        return factory(Performer::class, $count)->create();
    }

    protected function createThroughsForParent(Model $parent, int $count = 1): Collection
    {
        return factory(Member::class, $count)->create([
            'performer_id' => $parent->id,
        ]);
    }

    protected function createRelationsForThrough(Model $through, int $count = 1): Collection
    {
        $memberId = $through->id;
        $memberCollection = collect([$through]);

        return factory(Flight::class, $count)->create()->each(function (Flight $flight) use($memberCollection, $memberId) {
            $flight->members()->attach([$memberId]);
            $flight->setRelation('members', $memberCollection);
        });
    }

    protected function attachThroughsToRelated(Collection $throughs, Model $related): void
    {
        $related->members()->attach($throughs->pluck('id')->toArray());
    }

    protected function relationName(): string
    {
        return 'flights';
    }

    protected function relatedModel(): string
    {
        return Flight::class;
    }

    protected function prepareDatabase(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/BelongsToManyThrough');

        $this->withFactories(__DIR__ . '/../database/factories');
    }
}
