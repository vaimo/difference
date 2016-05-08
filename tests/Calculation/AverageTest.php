<?php

namespace Undemanding\Difference\Test\Calculation;

use Undemanding\Difference\Calculation\Average;
use Undemanding\Difference\Test\Test;

class AverageTest extends Test
{
    /**
     * @test
     */
    public function itCalculatesAverages()
    {
        $calculation = new Average();

        $this->assertEquals(2.5, $calculation([[1, 2], [3, 4]], 2, 2));
        $this->assertEquals(6.5, $calculation([[5, 6], [7, 8]], 2, 2));
    }
}
