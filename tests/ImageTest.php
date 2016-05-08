<?php

namespace Undemanding\Difference\Test;

use InvalidArgumentException;
use Undemanding\Difference\Difference;
use Undemanding\Difference\Image;

/**
 * @covers Undemanding\Difference\Image
 */
class ImageTest extends Test
{
    /**
     * @test
     */
    public function itCreatesImages()
    {
        $image = new Image(__DIR__ . "/fixtures/fez-1.png");

        $this->assertEquals(400, $image->getWidth());
        $this->assertEquals(300, $image->getHeight());

        $bitmap = $image->getBitmap();

        $this->assertInternalType("array", $bitmap);
        $this->assertEquals(300, count($bitmap));

        // test secondary types

        new Image(__DIR__ . "/fixtures/fez-1.jpg");
        new Image(__DIR__ . "/fixtures/fez-1.gif");
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function itThrowsForMissingImage()
    {
        new Image("does/not/exist");
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function itThrowsForInvalidImage()
    {
        new Image(__FILE__);
    }

    /**
     * @test
     */
    public function itDerivesDifferences()
    {
        $image1 = new Image(__DIR__ . "/fixtures/fez-1.png");
        $image2 = new Image(__DIR__ . "/fixtures/fez-1.png");

        $method = function ($p, $q) {
            return 0;
        };

        $this->assertInstanceOf(Difference::class, $image1->difference($image2, $method));
    }
}