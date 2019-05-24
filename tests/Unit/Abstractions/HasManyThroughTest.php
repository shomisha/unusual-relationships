<?php


namespace Shomisha\UnusualRelationships\Test\Unit\Abstractions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Shomisha\UnusualRelationships\Test\TestCase;

abstract class HasManyThroughTest extends TestCase
{
    use RefreshDatabase;

    /** @var \Illuminate\Database\Eloquent\Model */
    protected $parent;

    /** @var \Illuminate\Database\Eloquent\Model */
    protected $through;

    /**
     * Create the parent instances without storing them within the test class.
     *
     * @param int $count
     * @return \Illuminate\Support\Collection
     */
    protected abstract function createParents(int $count = 1): Collection;

    /**
     * Create the through instances without storing them within the test class.
     *
     * @param int $count
     * @return \Illuminate\Support\Collection
     */
    protected abstract function createThroughsForParent(Model $parent, int $count = 1): Collection;

    /**
     * Create relationship instances for the provided through instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $through
     * @param int $count
     * @return \Illuminate\Support\Collection
     */
    protected abstract function createRelationsForThrough(Model $through, int $count = 1): Collection;

    /**
     * Attach the specified throughs to the specified related model.
     *
     * @param \Illuminate\Support\Collection $throughs
     * @param \Illuminate\Database\Eloquent\Model $related
     * @return bool
     */
    protected abstract function attachThroughsToRelated(Collection $throughs, Model $related): void;

    /**
     * Returns the name of the relationship used within the parent model.
     *
     * @return string
     */
    protected abstract function relationName(): string;

    /**
     * Return the class name of the related model.
     *
     * @return string
     */
    protected abstract function relatedModel(): string;

    /**
     * A hook designed for loading migrations and factories.
     */
    protected abstract function prepareDatabase(): void;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareDatabase();

        $this->parent = $this->createParents()->first();
        $this->through = $this->createThroughsForParent($this->parent)->first();
    }

    /** @test */
    public function relationship_can_eager_load_related_models()
    {
        $relationName = $this->relationName();
        $related = $this->createRelationsForThrough($this->through)->first();
        $this->assertFalse($this->parent->relationLoaded($relationName));


        $this->parent->load($relationName);


        $this->assertTrue($this->parent->relationLoaded($relationName));

        $loadedRelateds = $this->parent->{$relationName};
        $this->assertCount(1, $loadedRelateds);
        $this->assertTrue($related->is($loadedRelateds->first()));
    }

    /** @test */
    public function relationship_can_load_related_models_automatically()
    {
        $relationName = $this->relationName();
        $related = $this->createRelationsForThrough($this->through)->first();
        $this->assertFalse($this->parent->relationLoaded($relationName));


        $this->parent->{$relationName};


        $this->assertTrue($this->parent->relationLoaded($relationName));

        $loadedRelateds = $this->parent->{$relationName};
        $this->assertCount(1, $loadedRelateds);
        $this->assertTrue($related->is($loadedRelateds->first()));
    }

    /** @test */
    public function relationship_can_eager_load_related_models_for_multiple_parent_models()
    {
        $relationName = $this->relationName();
        $parents = $this->createParents(5);
        $relateds = $parents->mapWithKeys(function (Model $parent) use ($relationName) {
            $this->assertFalse($parent->relationLoaded($relationName));

            $through = $this->createThroughsForParent($parent)->first();
            return [
                $parent->id => $this->createRelationsForThrough($through)->first()
            ];
        });


        $parents->load($relationName);


        $parents->each(function (Model $parent) use ($relationName, $relateds) {
            $this->assertTrue($parent->relationLoaded($relationName));

            $loadedRelates = $parent->{$relationName};
            $this->assertCount(1, $loadedRelates);
            $this->assertTrue($loadedRelates->first()->is($relateds->get($parent->id)));
        });
    }

    /** @test */
    public function relationship_will_return_a_collection_of_related_models()
    {
        $relationName = $this->relationName();
        $relateds = $this->createRelationsForThrough($this->through, 5);
        $this->assertFalse($this->parent->relationLoaded($relationName));

        $loadedRelateds = $this->parent->{$relationName};

        $this->assertTrue($this->parent->relationLoaded($relationName));
        $this->assertInstanceOf(Collection::class, $loadedRelateds);
        $this->assertCount(5, $loadedRelateds);
        $this->assertArrayValues($relateds->pluck('id'), $loadedRelateds->pluck('id'));
    }

    /** @test */
    public function only_one_related_model_will_be_returned_regardless_of_the_number_of_through_models()
    {
        $related = $this->createRelationsForThrough($this->through)->first();
        $moreThroughs = $this->createThroughsForParent($this->parent, 5);
        $this->attachThroughsToRelated($moreThroughs, $related);

        $loadedRelateds = $this->parent->{$this->relationName()};

        $this->assertCount(1, $loadedRelateds);
        $this->assertTrue($related->is($loadedRelateds->first()));
    }

    /** @test */
    public function relationship_will_not_return_non_related_models()
    {
        $anotherParent = $this->createParents()->first();
        $anotherThrough = $this->createThroughsForParent($anotherParent)->first();
        $anotherRelated = $this->createRelationsForThrough($anotherThrough)->first();

        $actualRelated = $this->createRelationsForThrough($this->through);


        $loadedRelateds = $this->parent->{$this->relationName()};


        $this->assertCount(1, $loadedRelateds);
        $this->assertNotContains($anotherRelated->id, $loadedRelateds->pluck('id'));
    }

    /** @test */
    public function relationship_will_return_an_empty_collection_when_no_related_models_exist()
    {
        $loadedRelateds = $this->parent->{$this->relationName()};

        $this->assertEmpty($loadedRelateds);
        $this->assertInstanceOf(Collection::class, $loadedRelateds);
    }
}