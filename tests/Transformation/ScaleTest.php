<?php

namespace Undemanding\Difference\Test\Transformation;

use Undemanding\Difference\Test\Test;
use Undemanding\Difference\Transformation\Difference;
use Undemanding\Difference\Transformation\Scale;

class ScaleTest extends Test
{
    /**
     * @test
     */
    public function itScalesDifferences()
    {
        $transformation = new Scale();

        $bitmap1 = [[0, 0], [0, 1]];
        $expected1 = [[0, 0], [0, 10]];

        $bitmap2 = [[2, 1], [0, 0]];
        $expected2 = [[20, 10], [0, 0]];

        $this->assertEquals($expected1, $transformation($bitmap1, 2, 2, 1, 10));
        $this->assertEquals($expected2, $transformation($bitmap2, 2, 2, 2, 20));
    }
}
