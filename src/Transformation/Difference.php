<?php

namespace Undemanding\Difference\Transformation;

class Difference
{
    /**
     * @var \Undemanding\Difference\Method\EuclideanDistance
     */
    private $defaultMethod;

    public function __construct()
    {
        $this->defaultMethod = new \Undemanding\Difference\Method\EuclideanDistance();
    }

    /**
     * Difference between all pixels of two images.
     *
     * @param array $bitmap1
     * @param array $bitmap2
     * @param int $width
     * @param int $height
     * @param array $offset
     * @param callable $method
     *
     * @return array
     */
    public function __invoke(array &$bitmap1, array &$bitmap2, $width, $height, $offset, callable $method = null)
    {
        if ($method == null) {
            $method = $this->defaultMethod;
        }

        $new = [];

        $os1 = [
            isset($bitmap1[$height - 1][$width]) ? $offset[0] : 0,
            isset($bitmap1[$height]) ? $offset[1] : 0
        ];

        $os2 = [
            isset($bitmap2[$height - 1][$width]) ? $offset[0] : 0,
            isset($bitmap2[$height]) ? $offset[1] : 0
        ];

        for ($y = 0; $y < $height; $y++) {
            $new[$y] = [];

            for ($x = 0; $x < $width; $x++) {
                $x1 = $x + $os1[0];
                $y1 = $y + $os1[1];
                $x2 = $x + $os2[0];
                $y2 = $y + $os2[1];

                if (!isset($bitmap1[$y1][$x1]) || !isset($bitmap2[$y2][$x2])) {
                    continue;
                }

                $new[$y][$x] = $method(
                    $bitmap1[$y1][$x1],
                    $bitmap2[$y2][$x2]
                );
            }
        }

        return $new;
    }
}
