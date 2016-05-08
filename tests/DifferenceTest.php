<?php

namespace Undemanding\Difference\Test;

use Undemanding\Difference\Image;
use Undemanding\Difference\Method\EuclideanDistance;

/**
 * @covers Undemanding\Difference\Difference
 */
class DifferenceTest extends Test
{
    /**
     * @test
     */
    public function itCalculatesBoundariesAndPercentages()
    {
        $image1 = new Image(__DIR__ . "/fixtures/fez-1.png");
        $image2 = new Image(__DIR__ . "/fixtures/fez-2.png"); // with text
        $image3 = new Image(__DIR__ . "/fixtures/fez-3.png"); // 1% transparent, with text

        $difference1 = $image1->difference($image2, new EuclideanDistance());

        $bitmap = $difference1->getBitmap();

        $this->assertInternalType("array", $bitmap);
        $this->assertEquals(300, count($bitmap));

        $boundary1 = [
            "left" => 122,
            "top" => 188,
            "width" => 138,
            "height" => 53,
        ];

        $this->assertEquals($boundary1, $difference1->boundary());
        $this->assertEquals(2.145, $difference1->percentage());

        $difference2 = $image1->difference($image3, new EuclideanDistance());

        $boundary2 = [
            "left" => 62,
            "top" => 0,
            "width" => 282,
            "height" => 286,
        ];

        $this->assertEquals($boundary2, $difference2->boundary());
        $this->assertEquals(34.1, round($difference2->percentage(), 1));

        $difference3 = $difference2->withScale(10)->withReducedStandardDeviation();

        $this->assertEquals($boundary1, $difference3->boundary());
        $this->assertEquals(2.0, round($difference3->percentage(), 1));
    }
}