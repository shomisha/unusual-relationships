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

    public function relationship_can_load_related_models_automatically()
    {

    }

    public function relationship_can_eager_load_related_models_for_multiple_parent_models()
    {

    }

    public function relationship_will_return_a_collection_of_related_models()
    {

    }

    public function only_one_related_model_will_be_returned_regardless_of_the_number_of_through_models()
    {

    }

    public function relationship_will_not_return_non_related_models()
    {

    }

    public function relationship_will_return_an_empty_collection_when_no_related_models_exist()
    {

    }
}