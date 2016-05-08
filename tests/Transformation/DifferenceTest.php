<?php

namespace Undemanding\Difference\Test\Transformation;

use Undemanding\Difference\Test\Test;
use Undemanding\Difference\Transformation\Difference;

class DifferenceTest extends Test
{
    /**
     * @test
     */
    public function itTransformsImagesToDifferences()
    {
        $transformation = new Difference();

        $bitmap1 = [[0, 0], [0, 1]];
        $bitmap2 = [[1, 0], [0, 0]];

        $method1 = function($p, $q) {
            return $p;
        };

        $method2 = function($p, $q) {
            return $q;
        };

        $this->assertEquals($bitmap1, $transformation($bitmap1, $bitmap2, 2, 2, $method1));
        $this->assertEquals($bitmap2, $transformation($bitmap1, $bitmap2, 2, 2, $method2));
    }
}
