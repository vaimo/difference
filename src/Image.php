<?php

namespace Undemanding\Difference;

use InvalidArgumentException;
use Undemanding\Difference\Transformation;

class Image
{
    /**
     * @var null|\resource
     */
    protected $resource;

    /**
     * @var array
     */
    protected $bitmap = [];

    /**
     * @param resource $image
     */
    public function __construct($image)
    {
        $this->resource = $image;
    }

    public function getCore()
    {
        return $this->resource;
    }

    public function reset()
    {
        unset($this->bitmap);

        $this->bitmap = [];
    }

    private function allocateBitmap(&$bitmap, $image, $width, $height)
    {
        for ($y = $this->getAllocatedHeight($bitmap); $y < $height; $y++) {
            $bitmap[$y] = [];

            for ($x = $this->getAllocatedWidth($bitmap); $x < $width; $x++) {
                $color = imagecolorat($image, $x, $y);

                $bitmap[$y][$x] = [
                    'r' => ($color >> 16) & 0xFF,
                    'g' => ($color >> 8) & 0xFF,
                    'b' => $color & 0xFF
                ];
            }
        }
    }

    /**
     * @param Image $image
     * @param callable $method
     * @param $offset
     *
     * @return Difference
     */
    public function difference(Image $image, array $offset = [0, 0], $method = null)
    {
        $transformation = new Transformation\Difference();

        $minWidth = min($this->getWidth(), $image->getWidth());
        $minHeight = min($this->getHeight(), $image->getHeight());

        $width = $minWidth + $offset[0];
        $height = $minHeight + $offset[1];

        $this->allocateBitmap(
            $this->bitmap,
            $this->resource,
            min($this->getWidth(), $width),
            min($this->getHeight(), $height)
        );

        $this->allocateBitmap(
            $image->bitmap,
            $image->resource,
            min($image->getWidth(), $width),
            min($image->getHeight(), $height)
        );

        $diffBitmap = $transformation($this->bitmap, $image->bitmap, $minWidth, $minHeight, $offset, $method);

        return new Difference($diffBitmap, $offset);
    }

    public function scaleBitmap($bitmap, $factor)
    {
        $maximum = $this->maximum($bitmap);
        $transformation = new Transformation\Scale();

        return $transformation(
            $bitmap,
            $this->getAllocatedWidth($bitmap),
            $this->getAllocatedHeight($bitmap),
            $maximum,
            $factor
        );
    }

    private function maximum($bitmap)
    {
        $calculation = new Calculation\Maximum();

        return $calculation(
            $bitmap,
            $this->getAllocatedWidth($bitmap),
            $this->getAllocatedHeight($bitmap)
        );
    }

    public function getWidth()
    {
        return imagesx($this->getCore());
    }

    public function getHeight()
    {
        return imagesy($this->getCore());
    }

    public function getAllocatedWidth(&$bitmap)
    {
        $height = $this->getAllocatedHeight($bitmap);

        end($bitmap[$height]);

        $width = key($bitmap[$height]) ?: 0;

        reset($bitmap[$height]);

        return $width;
    }

    public function getAllocatedHeight(&$bitmap)
    {
        end($bitmap);

        $height = key($bitmap) ?: 0;

        reset($bitmap);

        return $height;
    }
}
