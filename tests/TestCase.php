<?php


namespace Shomisha\UnusualRelationships\Test;

use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase as OrchestraTestCase;


class TestCase extends OrchestraTestCase
{
    /**
     * Assert that the two specified arrays have the same values.
     *
     * @param $expected
     * @param $original
     * @param string $message
     */
    protected function assertArrayValues($expected, $original, string $message = null)
    {
        if ($expected instanceof Collection) {
            $expected = $expected->toArray();
        }
        if ($original instanceof Collection) {
            $original = $original->toArray();
        }
        if ($message === null) {
            $message = "The two arrays do not match.";
        }

        $this->assertEmpty(array_diff($expected, $original), $message);
    }
}