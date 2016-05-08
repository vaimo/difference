<?php

namespace Undemanding\Difference\Test\Calculation;

use Undemanding\Difference\Calculation\Percentage;
use Undemanding\Difference\Test\Test;

class PercentageTest extends Test
{
    /**
     * @test
     */
    public function itCalculatesPercentages()
    {
        $calculation = new Percentage();

        $this->assertEquals(25, $calculation([[0, 1], [0, 0]], 2, 2));
        $this->assertEquals(50, $calculation([[0, 1], [1, 0]], 2, 2));
    }
}
