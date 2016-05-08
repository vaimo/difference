<?php

namespace Undemanding\Difference\Test\Method;

use Undemanding\Difference\Method\EuclideanDistance;
use Undemanding\Difference\Test\Test;

class EuclideanDistanceTest extends Test
{
    /**
     * @test
     */
    public function itCalculatesEuclideanDistance()
    {
        $method = new EuclideanDistance();

        $this->assertEquals(43.3, round($method(
            ["r" => 100, "g" => 125, "b" => 150],
            ["r" => 125, "g" => 150, "b" => 175]
        ), 1));

        $this->assertEquals(29.58, round($method(
            ["r" => 205, "g" => 210, "b" => 215],
            ["r" => 200, "g" => 195, "b" => 190]
        ), 2));
    }
}
