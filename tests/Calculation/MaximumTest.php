<?php

namespace Undemanding\Difference\Test\Calculation;

use Undemanding\Difference\Calculation\Maximum;
use Undemanding\Difference\Test\Test;

class MaximumTest extends Test
{
    /**
     * @test
     */
    public function itCalculatesMaximums()
    {
        $calculation = new Maximum();

        $this->assertEquals(4, $calculation([[1, 2], [3, 4]], 2, 2));
        $this->assertEquals(8, $calculation([[5, 6], [7, 8]], 2, 2));
    }
}
