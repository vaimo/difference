<?php

namespace Undemanding\Difference\Test\Transformation;

use Undemanding\Difference\Calculation\Average;
use Undemanding\Difference\Calculation\StandardDeviation;
use Undemanding\Difference\Test\Test;
use Undemanding\Difference\Transformation\ReducedStandardDeviation;

class ReducedStandardDeviationTest extends Test
{
    /**
     * @test
     */
    public function itTransformsStandardDeviationBitmapsToReducedStandardDeviationBitmaps()
    {
        $transformation = new ReducedStandardDeviation();

        $bitmap1 = [[0.25, 1], [0.25, 0.25]];
        $expected1 = [[0, 1], [0, 0]];

        $this->assertEquals($expected1, $transformation($bitmap1, 2, 2, 0.38));
    }
}
