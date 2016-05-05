<?php

namespace Undemanding\Difference;

use InvalidArgumentException;
use LogicException;

class State
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var array
     */
    private $map = [];

    /**
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * New state from image path.
     *
     * @param string $path
     *
     * @return State
     */
    public static function fromImage($path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("image not found");
        }

        $image = static::createImage($path);
        $width = imagesx($image);
        $height = imagesy($image);

        $map = [];

        for ($y = 0; $y < $height; $y++) {
            $map[$y] = [];

            for ($x = 0; $x < $width; $x++) {
                $color = imagecolorat($image, $x, $y);

                $map[$y][$x] = [
                    "red" => ($color >> 16) & 0xFF,
                    "green" => ($color >> 8) & 0xFF,
                    "blue" => $color & 0xFF
                ];
            }
        }

        $new = new static($width, $height);
        $new->map = $map;

        return $new;
    }

    /**
     * Create GD image handle.
     *
     * @param string $path
     *
     * @return resource
     *
     * @throws InvalidArgumentException
     */
    private static function createImage($path)
    {
        $info = getimagesize($path);
        $type = $info[2];

        $image = null;

        if ($type == IMAGETYPE_JPEG) {
            $image = imagecreatefromjpeg($path);
        }
        if ($type == IMAGETYPE_GIF) {
            $image = imagecreatefromgif($path);
        }
        if ($type == IMAGETYPE_PNG) {
            $image = imagecreatefrompng($path);
        }

        if (!$image) {
            throw new InvalidArgumentException("image invalid");
        }

        return $image;
    }

    /**
     * Difference between two bitmap states.
     *
     * @param State $state
     * @param callable $method
     *
     * @return State
     */
    public function withDifference(State $state, callable $method)
    {
        $map = [];

        for ($y = 0; $y < $this->height; $y++) {
            $map[$y] = [];

            for ($x = 0; $x < $this->width; $x++) {
                $map[$y][$x] = $method($this->map[$y][$x], $state->map[$y][$x]);
            }
        }

        return $this->cloneWith("map", $map);
    }

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return State
     */
    private function cloneWith($property, $value)
    {
        $clone = clone $this;
        $clone->$property = $value;

        return $clone;
    }

    /**
     * New state from reduced standard deviation.
     *
     * @return State
     */
    public function withReducedStandardDeviation()
    {
        $map = array_slice($this->map, 0);
        $deviation = $this->standardDeviation();

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if (abs($map[$y][$x]) < $deviation) {
                    $map[$y][$x] = 0;
                }
            }
        }

        return $this->cloneWith("map", $map);
    }

    /**
     * Standard deviation for map.
     *
     * @return float
     */
    public function standardDeviation()
    {
        $standardDeviation = 0;
        $average = $this->average();

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if (!is_numeric($this->map[$y][$x])) {
                    throw new LogicException("pixel is not numeric");
                }

                $delta = $this->map[$y][$x] - $average;
                $standardDeviation += ($delta * $delta);
            }
        }

        $standardDeviation /= (($this->width * $this->height) - 1);
        $standardDeviation = sqrt($standardDeviation);

        return $standardDeviation;
    }

    /**
     * Average pixel value.
     *
     * @return int
     */
    public function average()
    {
        $average = 0;

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if (!is_numeric($this->map[$y][$x])) {
                    throw new LogicException("pixel is not numeric");
                }

                $average += $this->map[$y][$x];
            }
        }

        $average /= ($this->width * $this->height);

        return $average;
    }

    /**
     * Scale by a factor.
     *
     * @param int $factor
     *
     * @return State
     */
    public function withScale($factor)
    {
        $maximum = $this->maximum();
        $map = array_slice($this->map, 0);

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if (!is_numeric($map[$y][$x])) {
                    throw new LogicException("pixel is not numeric");
                }

                $map[$y][$x] = ($map[$y][$x] / $maximum) * $factor;
            }
        }

        return $this->cloneWith("map", $map);
    }

    /**
     * Maximum pixel value.
     *
     * @return int
     */
    public function maximum()
    {
        $maximum = 0;

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if (!is_numeric($this->map[$y][$x])) {
                    throw new LogicException("pixel is not numeric");
                }

                if ($this->map[$y][$x] > $maximum) {
                    $maximum = $this->map[$y][$x];
                }
            }
        }

        return $maximum;
    }

    /**
     * New state with pixel values rounded.
     *
     * @param int $precision
     *
     * @return State
     */
    public function withRounding($precision)
    {
        $map = array_slice($this->map, 0);

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if (!is_numeric($map[$y][$x])) {
                    throw new LogicException("pixel is not numeric");
                }

                $map[$y][$x] = round($map[$y][$x], $precision);
            }
        }

        return $this->cloneWith("map", $map);
    }

    /**
     * New state with absolute pixel values.
     *
     * @return State
     */
    public function withAbsolute()
    {
        $map = array_slice($this->map, 0);

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if (!is_numeric($map[$y][$x])) {
                    throw new LogicException("pixel is not numeric");
                }

                $map[$y][$x] = abs($map[$y][$x]);
            }
        }

        return $this->cloneWith("map", $map);
    }

    /**
     * Boundary for all significant pixels.
     *
     * @return array
     *
     * @throws LogicException
     */
    public function boundary()
    {
        $ax = $this->width;
        $bx = 0;
        $ay = $this->width;
        $by = 0;

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if ($this->map[$y][$x] > 0) {
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

        if ($ax > $bx) {
            throw new LogicException("ax is greater than bx");
        }

        if ($ay > $by) {
            throw new LogicException("ay is greater than by");
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
}
