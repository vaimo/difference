<?php

namespace Undemanding\Difference;

use Undemanding\Difference\Calculation;
use Undemanding\Difference\Transformation;

class Difference
{
    /**
     * @var array
     */
    private $bitmap;

    /**
     * @var array
     */
    private $offset;

    /**
     * @var int
     */
    private $height;

    /**
     * @var int
     */
    private $width;

    public function __construct(array &$bitmap, $offset)
    {
        $this->bitmap = $bitmap;
        $this->offset = $offset;

        end($bitmap);
        $this->height = key($bitmap);

        end($bitmap[$this->height]);
        $this->width = key($bitmap[$this->height]);

        reset($bitmap);
        reset($bitmap[$this->height]);
    }

    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return array
     */
    public function getBitmap()
    {
        return $this->bitmap;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    /**
     * New Difference from reduced standard deviation.
     *
     * @return Difference
     */
    public function withReducedStandardDeviation()
    {
        $deviation = $this->standardDeviation();
        $transformation = new Transformation\ReducedStandardDeviation();

        $bitmap = $transformation(
            $this->bitmap,
            $this->width,
            $this->height,
            $deviation
        );

        return $this->cloneWith("bitmap", $bitmap);
    }

    /**
     * Standard deviation for all bitmap pixels.
     *
     * @return float
     */
    private function standardDeviation()
    {
        $average = $this->average();
        $calculation = new Calculation\StandardDeviation();

        return $calculation(
            $this->bitmap, $this->width, $this->height, $average
        );
    }

    /**
     * Average difference for all bitmap pixels.
     *
     * @return int
     */
    private function average()
    {
        $calculation = new Calculation\Average();

        return $calculation(
            $this->bitmap, $this->width, $this->height
        );
    }

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return Difference
     */
    private function cloneWith($property, $value)
    {
        $clone = clone $this;
        $clone->$property = $value;

        return $clone;
    }

    /**
     * Boundary for all significant pixels.
     *
     * @return array
     */
    public function boundary()
    {
        $ax = $this->width;
        $bx = 0;
        $ay = $this->width;
        $by = 0;

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if ($this->bitmap[$y][$x] > 0) {
                    if ($x > $bx) {
                        $bx = $x;
                    }

                    if ($x < $ax) {
                        $ax = $x;
                    }

                    if ($y > $by) {
                        $by = $y;
                    }

                    if ($y < $ay) {
                        $ay = $y;
                    }
                }
            }
        }

        $ax = ($ax / $this->width) * $this->width;
        $bx = ((($bx + 1) / $this->width) * $this->width) - $ax;
        $ay = ($ay / $this->height) * $this->height;
        $by = ((($by + 1) / $this->height) * $this->height) - $ay;

        return [
            "left" => $ax,
            "top" => $ay,
            "width" => $bx,
            "height" => $by
        ];
    }

    /**
     * Percentage of different pixels in the bitmap.
     *
     * @return float
     */
    public function percentage()
    {
        $calculation = new Calculation\Percentage();

        return $calculation(
            $this->bitmap,
            $this->width,
            $this->height
        );
    }
}
