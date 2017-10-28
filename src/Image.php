<?php

namespace Undemanding\Difference;

use InvalidArgumentException;
use Undemanding\Difference\Transformation;

class Image
{
    /**
     * @var string
     */
    private $imagePath;

    /**
     * @var null|\resource
     */
    private $image;

    /**
     * @var array
     */
    private $bitmap = [];

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("image not found");
        }

        $this->imagePath = $path;
    }

    public function getCore()
    {
        if ($this->image === null) {
            $this->image = $this->loadImage($this->imagePath);
        }

        return $this->image;
    }

    public function destroy()
    {
        if ($this->image === null) {
            return;
        }

        unset($this->bitmap);
        imagedestroy($this->image);
    }

    public function getBitmap($width = 0, $height = 0)
    {
        $image = $this->getCore();

        $this->allocateBitmap(
            $image,
            $width ?: imagesx($image),
            $height ?: imagesy($image)
        );

        return $this->bitmap;
    }

    /**
     * Create new image resource from image path.
     *
     * @param string $path
     *
     * @return resource
     *
     * @throws InvalidArgumentException
     */
    private function loadImage($path)
    {
        $info = getimagesize($path);

        switch ($type = $info[2]) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
        }

        throw new InvalidArgumentException(sprintf("Invalid image type: %s", $type));
    }

    /**
     * Creates new bitmap from image resource.
     *
     * @param resource $image
     * @param int $width
     * @param int $height
     *
     * @return array
     */
    private function allocateBitmap($image, $width, $height)
    {
        for ($y = $this->getAllocatedHeight(); $y < $height; $y++) {
            $this->bitmap[$y] = [];

            for ($x = $this->getAllocatedWidth(); $x < $width; $x++) {
                $color = imagecolorat($image, $x, $y);

                $this->bitmap[$y][$x] = [
                    "r" => ($color >> 16) & 0xFF,
                    "g" => ($color >> 8) & 0xFF,
                    "b" => $color & 0xFF
                ];
            }
        }
    }

    /**
     * Difference between two bitmap states.
     *
     * @param Image $image
     * @param callable $method
     *
     * @return Difference
     */
    public function difference(Image $image, callable $method)
    {
        $transformation = new Transformation\Difference();

        $minWidth = min($this->getWidth(), $image->getWidth());
        $minHeight = min($this->getHeight(), $image->getHeight());

        $bitmap = $transformation(
            $this->getBitmap($minWidth, $minHeight),
            $image->getBitmap($minWidth, $minHeight),
            $minWidth,
            $minHeight,
            $method
        );

        return new Difference($bitmap);
    }

    public function getWidth()
    {
        return imagesx($this->getCore());
    }

    public function getHeight()
    {
        return imagesy($this->getCore());
    }

    public function getAllocatedWidth()
    {
        $height = $this->getAllocatedHeight();

        end($this->bitmap[$height]);

        $width = key($this->bitmap[$height]) ?: 0;

        reset($this->bitmap[$height]);

        return $width;
    }

    public function getAllocatedHeight()
    {
        end($this->bitmap);

        $height = key($this->bitmap) ?: 0;

        reset($this->bitmap);

        return $height;
    }
}
