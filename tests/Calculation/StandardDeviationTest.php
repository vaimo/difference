<?php

namespace Undemanding\Difference\Test\Calculation;

use Undemanding\Difference\Calculation\StandardDeviation;
use Undemanding\Difference\Test\Test;

class StandardDeviationTest extends Test
{
    /**
     * @test
     */
    public function itCalculatesStandardDeviations()
    {
        $calculation = new StandardDeviation();

        $this->assertEquals(0.5, $calculation([[0, 1], [0, 0]], 2, 2, 0.25));
        $this->assertEquals(0.58, round($calculation([[0, 1], [1, 0]], 2, 2, 0.5), 2));
    }
}
